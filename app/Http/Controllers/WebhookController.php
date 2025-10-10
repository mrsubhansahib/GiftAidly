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

            // if ($local) {
            //     $local->status      = 'canceled';
            //     $local->canceled_at = now();
            //     $local->save();
            // }
            Mail::to(config('mail.admin_email'))->send(new SubscriptionCanceledMail($local, true)); 
            Mail::to($local->user->email)->send(new SubscriptionCanceledMail($local)); 
        });
    }

    /* ---------- INVOICE EVENTS ---------- */

    private function onInvoicePaymentSucceeded($inv)
    {
        DB::transaction(function () use ($inv) {
            $subscription = Subscription::where('stripe_subscription_id', $inv->subscription)->first();
            if (!$subscription) return;

            $invoice = Invoice::firstOrNew(['stripe_invoice_id' => $inv->id]);
            $invoice->subscription_id = $subscription->id;
            $invoice->amount_due      = $this->amount($inv->amount_due, $inv->currency);
            $invoice->currency        = $inv->currency;
            $invoice->invoice_date    = $this->ts($inv->created);
            $invoice->paid_at         = now();
            $invoice->save();

            Transaction::updateOrCreate(
                ['stripe_transaction_id' => (string)$inv->payment_intent],
                ['invoice_id' => $invoice->id, 'status' => 'paid', 'paid_at' => now()]
            );

            // ✅ Extra Scenario: Cancel if local DB says subscription ended earlier
            if ($subscription->canceled_at && now()->gte($subscription->canceled_at)) {
                if ($subscription->status !== 'canceled') {
                    $subscription->update(['status' => 'canceled']);
                    try {
                        StripeSubscription::update($inv->subscription, [
                            'cancel_at_period_end' => true,
                        ]);
                    } catch (\Throwable $e) {
                        Log::warning("Failed to sync early cancel to Stripe: " . $e->getMessage());
                    }
                }
            }
        });
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
