<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Transaction;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 1) Verify Stripe signature
        $signature = $request->header('Stripe-Signature');
        $payload   = $request->getContent();
        $secret    = config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET'));

        try {
            // If you installed stripe/stripe-php:^14, you can use \Stripe\Webhook
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // 2) Route by event type
        try {
            switch ($event->type) {
                /* ---------- SUBSCRIPTION LIFECYCLE ---------- */
                case 'customer.subscription.created':
                    $this->onSubscriptionCreated($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->onSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->onSubscriptionDeleted($event->data->object);
                    break;

                /* ---------- INVOICES ---------- */
                case 'invoice.created':
                    $this->onInvoiceCreated($event->data->object);
                    break;

                case 'invoice.finalized':
                    $this->onInvoiceFinalized($event->data->object);
                    break;

                case 'invoice.payment_succeeded':
                    $this->onInvoicePaymentSucceeded($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->onInvoicePaymentFailed($event->data->object);
                    break;

                /* ---------- PAYMENTS (extra safety / one-off) ---------- */
                case 'payment_intent.succeeded':
                    $this->onPaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->onPaymentIntentFailed($event->data->object);
                    break;

                /* ---------- REFUNDS (optional) ---------- */
                case 'charge.refunded':
                    $this->onChargeRefunded($event->data->object);
                    break;

                default:
                    // Not critical for your flow; just acknowledge
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // A 500 makes Stripe retry — useful if your DB/email temporarily failed
            return response('Webhook handler error', 500);
        }

        // 3) ACK
        return response('OK', 200);
    }

    /* ============================================================
       ===============  EVENT HANDLERS (PRIVATE)  =================
       ============================================================ */

    /** customer.subscription.created */
    private function onSubscriptionCreated($sub)
    {
        DB::transaction(function () use ($sub) {
            // Find user by customer id
            $user = User::where('stripe_customer_id', $sub->customer)->first();
            if (!$user) return;

            // Upsert subscription
            $local = Subscription::firstOrNew(['stripe_subscription_id' => $sub->id]);
            $local->user_id            = $user->id;
            $local->stripe_price_id    = optional($sub->items->data[0] ?? null)->price->id ?? null;
            $local->status             = $sub->status; // e.g. trialing/active/incomplete
            $local->price              = $this->priceAmountFromSub($sub); // your earlier saved price
            $local->currency           = optional($sub->items->data[0] ?? null)->price->currency ?? 'gbp';
            $local->type               = optional($sub->items->data[0] ?? null)->price->recurring->interval ?? 'month';
            $local->start_date         = $this->ts($sub->current_period_start);
            $local->end_date           = $this->ts($sub->current_period_end);
            $local->canceled_at        = $this->ts($sub->cancel_at);
            $local->save();

            // Send "subscription started/scheduled" mail
            $msg = "Your donation subscription has started/scheduled.\n"
                 . "Plan: {$local->type}\n"
                 . "From: {$local->start_date}\n"
                 . "To: {$local->end_date}";
            $this->mailUser($user->email, 'Donation subscription created', $msg);
        });
    }

    /** customer.subscription.updated */
    private function onSubscriptionUpdated($sub)
    {
        DB::transaction(function () use ($sub) {
            $local = Subscription::where('stripe_subscription_id', $sub->id)->first();
            if (!$local) return;

            // Track old status to detect transitions
            $oldStatus = $local->status;

            $local->status      = $sub->status;
            $local->start_date  = $this->ts($sub->current_period_start);
            $local->end_date    = $this->ts($sub->current_period_end);
            $local->canceled_at = $this->ts($sub->cancel_at);
            $local->save();

            $user = $local->user ?? null;

            // Notify on cancellation scheduled or paused, etc.
            if ($oldStatus !== 'canceled' && $sub->status === 'canceled') {
                if ($user) {
                    $this->mailUser(
                        $user->email,
                        'Donation subscription canceled',
                        "Your donation subscription was canceled.\n"
                        ."Cancellation date: {$local->canceled_at}"
                    );
                }
            }

            // If period ended (some setups send an update), send "ended"
            if ($user && $sub->status === 'canceled' && $local->end_date && now()->gte($local->end_date)) {
                $this->mailUser(
                    $user->email,
                    'Donation period ended',
                    "Your donation subscription period has ended on {$local->end_date}."
                );
            }
        });
    }

    /** customer.subscription.deleted (immediate cancellation) */
    private function onSubscriptionDeleted($sub)
    {
        DB::transaction(function () use ($sub) {
            $local = Subscription::where('stripe_subscription_id', $sub->id)->first();
            if (!$local) return;

            $local->status      = 'canceled';
            $local->canceled_at = now();
            $local->save();

            if ($local->user) {
                $this->mailUser(
                    $local->user->email,
                    'Donation subscription canceled',
                    'Your donation subscription was canceled.'
                );
            }
        });
    }

    /** invoice.created — create local invoice & email "new invoice created" */
    private function onInvoiceCreated($inv)
    {
        DB::transaction(function () use ($inv) {
            $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
            if (!$subscription) return;

            $invoice = Invoice::firstOrNew(['stripe_invoice_id' => $inv->id]);
            $invoice->subscription_id = $subscription->id;
            $invoice->amount_due      = $this->amount($inv->amount_due, $inv->currency);
            $invoice->currency        = $inv->currency;
            $invoice->invoice_date    = $this->ts($inv->created);
            // paid_at stays null; finalized event will set more details
            $invoice->save();

            if ($subscription->user) {
                $this->mailUser(
                    $subscription->user->email,
                    'New donation invoice created',
                    "A new invoice was created for your donation.\nInvoice #: {$inv->number}\nAmount: {$invoice->amount_due} {$invoice->currency}"
                );
            }
        });
    }

    /** invoice.finalized — mark as finalized (still unpaid) */
    private function onInvoiceFinalized($inv)
    {
        DB::transaction(function () use ($inv) {
            $invoice = Invoice::where('stripe_invoice_id', $inv->id)->first();
            if (!$invoice) {
                // If created event missed, create now
                $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
                if (!$subscription) return;
                $invoice = new Invoice();
                $invoice->stripe_invoice_id = $inv->id;
                $invoice->subscription_id   = $subscription->id;
            }

            $invoice->amount_due   = $this->amount($inv->amount_due, $inv->currency);
            $invoice->currency     = $inv->currency;
            $invoice->invoice_date = $this->ts($inv->created);
            $invoice->save();
        });
    }

    /** invoice.payment_succeeded — mark invoice paid, create transaction, email receipt */
    private function onInvoicePaymentSucceeded($inv)
    {
        DB::transaction(function () use ($inv) {
            $invoice = Invoice::where('stripe_invoice_id', $inv->id)->first();
            if (!$invoice) {
                // create if missing
                $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
                if (!$subscription) return;
                $invoice = new Invoice();
                $invoice->stripe_invoice_id = $inv->id;
                $invoice->subscription_id   = $subscription->id;
                $invoice->amount_due        = $this->amount($inv->amount_due, $inv->currency);
                $invoice->currency          = $inv->currency;
                $invoice->invoice_date      = $this->ts($inv->created);
            }

            $invoice->paid_at = $this->ts($inv->status_transitions->paid_at ?? $inv->created);
            $invoice->save();

            // Record transaction (use PaymentIntent as the "transaction id")
            $pi = $inv->payment_intent ?? null;
            $tx = Transaction::firstOrNew(['stripe_transaction_id' => (string)$pi]);
            $tx->invoice_id = $invoice->id;
            $tx->paid_at    = $this->ts($inv->status_transitions->paid_at ?? $inv->created);
            $tx->status     = 'succeeded';
            $tx->save();

            $subscription = $invoice->subscription;
            if ($subscription && $subscription->user) {
                $this->mailUser(
                    $subscription->user->email,
                    'Donation payment received',
                    "Thank you! Your donation invoice has been paid.\nInvoice: {$inv->number}\nAmount: {$invoice->amount_due} {$invoice->currency}"
                );
            }
        });
    }

    /** invoice.payment_failed — record failed transaction, email failure */
    private function onInvoicePaymentFailed($inv)
    {
        DB::transaction(function () use ($inv) {
            $invoice = Invoice::where('stripe_invoice_id', $inv->id)->first();
            if (!$invoice) {
                // create minimal invoice if missing
                $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
                if (!$subscription) return;
                $invoice = new Invoice();
                $invoice->stripe_invoice_id = $inv->id;
                $invoice->subscription_id   = $subscription->id;
                $invoice->amount_due        = $this->amount($inv->amount_due, $inv->currency);
                $invoice->currency          = $inv->currency;
                $invoice->invoice_date      = $this->ts($inv->created);
                $invoice->save();
            }

            $pi = $inv->payment_intent ?? null;
            $tx = Transaction::firstOrNew(['stripe_transaction_id' => (string)$pi]);
            $tx->invoice_id = $invoice->id;
            $tx->status     = 'failed';
            $tx->paid_at    = null;
            $tx->save();

            $subscription = $invoice->subscription;
            if ($subscription && $subscription->user) {
                $this->mailUser(
                    $subscription->user->email,
                    'Donation payment failed',
                    "We couldn't process your donation invoice (Invoice: {$inv->number}). "
                    ."Stripe will retry automatically. If this continues, please update your payment method."
                );
            }
        });
    }

    /** payment_intent.succeeded — extra guard (useful for one-offs) */
    private function onPaymentIntentSucceeded($pi)
    {
        // If the PI is linked to an invoice, invoice handler already did it.
        if (!empty($pi->invoice)) {
            // ensure tx is present
            $inv = Invoice::where('stripe_invoice_id', $pi->invoice)->first();
            if ($inv) {
                Transaction::firstOrCreate(
                    ['stripe_transaction_id' => $pi->id],
                    ['invoice_id' => $inv->id, 'status' => 'succeeded', 'paid_at' => now()]
                );
            }
            return;
        }
        // Handle non-invoice payments if you ever add them (optional)
    }

    /** payment_intent.payment_failed — extra guard */
    private function onPaymentIntentFailed($pi)
    {
        if (!empty($pi->invoice)) {
            $inv = Invoice::where('stripe_invoice_id', $pi->invoice)->first();
            if ($inv) {
                Transaction::updateOrCreate(
                    ['stripe_transaction_id' => $pi->id],
                    ['invoice_id' => $inv->id, 'status' => 'failed', 'paid_at' => null]
                );
            }
        }
    }

    /** charge.refunded — mark a refund as a transaction state (optional) */
    private function onChargeRefunded($charge)
    {
        // Refunds usually correspond to a PaymentIntent
        $piId = $charge->payment_intent ?? null;
        if (!$piId) return;

        $invId = $charge->invoice ?? null;
        if (!$invId) return;

        $invoice = Invoice::where('stripe_invoice_id', $invId)->first();
        if (!$invoice) return;

        Transaction::updateOrCreate(
            ['stripe_transaction_id' => (string)$piId],
            ['invoice_id' => $invoice->id, 'status' => 'refunded', 'paid_at' => null]
        );

        $subscription = $invoice->subscription;
        if ($subscription && $subscription->user) {
            $this->mailUser(
                $subscription->user->email,
                'Donation refunded',
                "A refund was processed for your donation invoice #{$charge->invoice}."
            );
        }
    }

    /* ==================== helpers ==================== */

    private function ts($unixOrNull)
    {
        return $unixOrNull ? now()->setTimestamp($unixOrNull) : null;
    }

    private function amount($inCents, $currency)
    {
        // store in major units to match your schema (string)
        return number_format(($inCents ?? 0) / 100, 2, '.', '');
    }

    private function priceAmountFromSub($sub)
    {
        // If you want to persist price in major units
        $price = optional($sub->items->data[0] ?? null)->price;
        return $price ? number_format($price->unit_amount / 100, 2, '.', '') : null;
    }

    private function mailUser(string $email, string $subject, string $body)
    {
        try {
            Mail::raw($body, function ($m) use ($email, $subject) {
                $m->to($email)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('Email send failed: '.$e->getMessage());
        }
    }
}
