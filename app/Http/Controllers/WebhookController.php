<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Webhook as StripeWebhook;
use Stripe\Subscription as StripeSubscription;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Mail\SubscriptionCanceledMail;
use App\Mail\TransactionPaidMail;
use Stripe\Charge;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        $payload   = $request->getContent();
        $secret    = config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET'));

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $event = StripeWebhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        try {
            switch ($event->type) {
                case 'customer.subscription.created':
                    $this->onSubscriptionCreated($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->onSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->onSubscriptionDeleted($event->data->object);
                    break;

                case 'invoice.payment_succeeded':
                    $this->onInvoicePaymentSucceeded($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->onInvoicePaymentFailed($event->data->object);
                    break;
                case 'charge.succeeded':
                    $this->onChargeSucceeded($event->data->object);
                    break;
                case 'payment_intent.succeeded':
                    $this->onPaymentIntentSucceeded($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->onChargeRefunded($event->data->object);
                    break;

                default:
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response('Webhook handler error', 500);
        }

        return response('OK', 200);
    }

    /* ---------- SUBSCRIPTION EVENTS ---------- */

    private function onSubscriptionCreated($sub)
    {
        // DB::transaction(function () use ($sub) {
        //     $user = User::where('stripe_customer_id', $sub->customer)->first();
        //     Log::info('User:'. $user->name);
        //     if (!$user) return;
        // });
    }

    private function onSubscriptionUpdated($sub)
    {
        DB::transaction(function () use ($sub) {
            $local = Subscription::where('stripe_subscription_id', $sub->id)->first();
            if (!$local) return;

            $local->status      = $sub->status;
            $local->start_date  = $this->ts($sub->current_period_start);
            $local->end_date    = $this->ts($sub->current_period_end);
            $local->canceled_at = $this->ts($sub->cancel_at);
            $local->save();
        });
    }

    private function onSubscriptionDeleted($sub)
    {
        DB::transaction(function () use ($sub) {
            $local = Subscription::where('stripe_subscription_id', $sub->id)->first();
            // Log::info('Subscription was:'. $local->user->name);

            Mail::to('lionsubhan123@gmail.com')->send(new SubscriptionCanceledMail($local->user));
            Mail::to($local->user->email)->send(new SubscriptionCanceledMail($local->user));
        });
    }

    /* ---------- INVOICE EVENTS ---------- */



    private function onInvoicePaymentSucceeded($inv)
    {
        $invoice = Invoice::where('stripe_invoice_id', $inv->id)->first();

        // if (!$invoice) {
        //     Log::warning("Local invoice not found for Stripe invoice {$inv->id}");
        //     return;
        // }

        // // 1️⃣ Check if charge exists
        // if (empty($inv->charge)) {
        //     Log::warning("Charge still missing, requeueing job for invoice {$inv->id}");
        //     self::dispatch($inv->id)->delay(now()->addMinutes(2));
        //     return;
        // }
        // $inv = \Stripe\Invoice::retrieve($invoice->stripe_invoice_id);
        // if (!$inv->charge) {
        //     // still missing — requeue again
        //     Log::warning("Charge still missing, requeueing job for invoice {$inv->id}");
        //     self::dispatch($inv->id)->delay(now()->addMinutes(2));
        //     return;
        // }

        // // 2️⃣ Retrieve charge object
        // try {
        //     $charge = Charge::retrieve($inv->charge);
        // } catch (\Exception $e) {
        //     Log::error("Failed to retrieve charge for invoice {$inv->id}: " . $e->getMessage());
        //     return;
        // }

        // // 3️⃣ Avoid duplicate transactions
        // if (Transaction::where('stripe_transaction_id', $charge->id)->exists()) {
        //     Log::info("Transaction already exists for charge {$charge->id}");
        //     return;
        // }

        // // 4️⃣ Create transaction record
        // $trans = Transaction::create([
        //     'invoice_id'            => $invoice->id,
        //     'stripe_transaction_id' => $charge->id,
        //     'status'                => 'paid',
        //     'paid_at'               => now(),
        // ]);

        // Log::info("✅ Transaction created with ID: {$trans->id} for charge {$charge->id}");

        // // 5️⃣ Send mail
        // Mail::to('lionsubhan123@gmail.com')
        //     ->send(new TransactionPaidMail($invoice->subscription->user, $trans, true));
    }

    private function onInvoicePaymentFailed($inv)
    {
        DB::transaction(function () use ($inv) {
            $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
            if (!$subscription) return;

            $invoice = Invoice::firstOrNew(['stripe_invoice_id' => $inv->id]);
            $invoice->subscription_id = $subscription->id;
            $invoice->amount_due      = $this->amount($inv->amount_due, $inv->currency);
            $invoice->currency        = $inv->currency;
            $invoice->invoice_date    = $this->ts($inv->created);
            $invoice->save();

            Transaction::updateOrCreate(
                ['stripe_transaction_id' => (string)$inv->payment_intent],
                ['invoice_id' => $invoice->id, 'status' => 'failed']
            );
        });
    }
    private function onChargeSucceeded($event)
    {
        $intent = $event['data']['object']; // PaymentIntent object
        $charge = $intent['charges']['data'][0] ?? null;
        Log::info('Charge Succeeded Event Received: ' . $intent['id']);
        if (!$charge) {
            Log::warning("No charge found for PaymentIntent {$intent['id']}");
            return;
        }

        $invoiceId = $charge['invoice'] ?? $intent['invoice'] ?? null;
        if (!$invoiceId) {
            Log::warning("No invoice linked to PaymentIntent {$intent['id']}");
            return;
        }

        // 1️⃣ Find local invoice
        $invoice = \App\Models\Invoice::where('stripe_invoice_id', $invoiceId)->first();
        if (!$invoice) {
            Log::warning("Local invoice not found for Stripe invoice ID: {$invoiceId}");
            return;
        }

        // 2️⃣ Prevent duplicates
        if (\App\Models\Transaction::where('stripe_transaction_id', $charge['id'])->exists()) {
            Log::info("Transaction already exists for charge {$charge['id']}");
            return;
        }

        // 3️⃣ Create Transaction
        $transaction = \App\Models\Transaction::create([
            'invoice_id'            => $invoice->id,
            'stripe_transaction_id' => $charge['id'],
            'status'                => 'paid',
            'paid_at'               => now(),
            'amount'                => $charge['amount'] / 100,
            'currency'              => strtoupper($charge['currency']),
        ]);

        // 4️⃣ Log / Notify
        Log::info("Transaction created for invoice {$invoiceId} - charge {$charge['id']}");

        // Optional: notify user/admin
    }
    /* ---------- PAYMENT EVENTS ---------- */

    private function onPaymentIntentSucceeded($pi)
    {
        if (!empty($pi->invoice)) return; // already handled
        // Handle one-time donations here if needed
    }

    private function onChargeRefunded($charge)
    {
        if (!$charge->invoice) return;
        $invoice = Invoice::where('stripe_invoice_id', $charge->invoice)->first();
        if (!$invoice) return;

        Transaction::updateOrCreate(
            ['stripe_transaction_id' => (string)$charge->payment_intent],
            ['invoice_id' => $invoice->id, 'status' => 'refunded']
        );
    }

    /* ---------- Helpers ---------- */

    private function ts($unixOrNull)
    {
        return $unixOrNull ? Carbon::createFromTimestamp($unixOrNull) : null;
    }

    private function amount($inCents, $currency)
    {
        return number_format(($inCents ?? 0) / 100, 2, '.', '');
    }
}
