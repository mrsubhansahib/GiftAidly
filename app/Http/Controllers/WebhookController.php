<?php

namespace App\Http\Controllers;

use App\Mail\InvoicePaidMail;
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
use App\Mail\TransactionFailedMail;
use Stripe\Charge;
use Stripe\Invoice as StripeInvoice;

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

                case 'invoice.created':
                    $this->onInvoiceCreated($event->data->object);
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
                case 'invoice_payment.paid':
                    $this->onInvoicePaymentPaid($event->data->object);
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
        //
    }


    private function onSubscriptionUpdated($sub)
    {
        // DB::transaction(function () use ($sub) {
        //     $local = Subscription::where('stripe_subscription_id', $sub->id)->first();
        //     if (!$local) return;

        //     $local->status      = $sub->status;
        //     $local->start_date  = $this->ts($sub->current_period_start);
        //     $local->end_date    = $this->ts($sub->current_period_end);
        //     $local->canceled_at = $this->ts($sub->cancel_at);
        //     $local->save();
        // });
    }

    private function onSubscriptionDeleted($sub)
    {
        DB::transaction(function () use ($sub) {
            $local = Subscription::where('stripe_subscription_id', $sub->id)->first();

            Mail::to(env('ADMIN_EMAIL'))->send(new SubscriptionCanceledMail($local, true));
            Mail::to($local->user->email)->send(new SubscriptionCanceledMail($local));
        });
    }

    /* ---------- INVOICE EVENTS ---------- */


    private function onInvoiceCreated($inv)
    {
        if (($inv->amount_paid / 100) > 0) {
            $invoice = StripeInvoice::retrieve([
                'id' => $inv->id,
                'expand' => ['payment_intent', 'charge', 'subscription'],
            ]);
            $subscription = Subscription::where('stripe_subscription_id', $invoice->lines->data[0]->parent->subscription_item_details->subscription)->first();
            $localInvoice = Invoice::updateOrCreate(
                [
                    'stripe_invoice_id' => $inv->id,
                    'subscription_id' => $subscription->id,
                    'amount_due'      => $inv->amount_paid / 100,
                    'currency'        => $inv->currency,
                    'paid_at'        => $this->ts($inv->status_transitions->paid_at ?? now()),
                    'invoice_date'    => $this->ts($inv->created),
                ]
            );
            // if ($inv->status === 'draft') {
            //     $inv = $inv->finalizeInvoice(); // instance method
            // } else if ($inv->collection_method === 'charge_automatically' && $inv->status !== 'paid') {
            //     $inv = $inv->pay(); // instance method
            // } else {
            //     Log::info("Invoice is already Paid");
            // }
        }else{
            Log::info("Invoice with 0 amount created: {$inv->id}");
        }
    }
    private function onInvoicePaymentSucceeded($inv)
    {
        // Log::info("Invoice Payment Succeeded Event Received: {$inv}");

        // Prevent duplicates
        // Fetch the full invoice object including payment intent
        // if (Invoice::where('stripe_invoice_id', $inv->id)->exists()) {
        //     Log::info("Invoice {$inv->id} already processed.");
        //     return;
        // }
        // $invoice = StripeInvoice::retrieve([
        //     'id' => $inv->id,
        //     'expand' => ['payment_intent', 'charge', 'subscription'],
        // ]);
        // Log::info("Invoice Payment Succeeded Event Received: {$invoice->id}");
        // // Now you have:
        // // $invoice->payment_intent->id  ✅
        // // $invoice->subscription->id    ✅ (if recurring)
        // // $invoice->charge              ✅
        // // $invoice->total, etc.         ✅

        // // DB::transaction(function () use ($invoice) {
        //     $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription->id)->first();

        //     $localInvoice = Invoice::updateOrCreate(
        //         ['stripe_invoice_id' => $invoice->id],
        //         [
        //             'subscription_id' => optional($subscription)->id,
        //             'amount_due'      => $invoice->total / 100,
        //             'currency'        => $invoice->currency,
        //             'status'          => 'paid',
        //             'invoice_date'    => Carbon::createFromTimestamp($invoice->created),
        //         ]
        //     );
        //     if ($invoice->payment_intent) {
        //         $trans = Transaction::updateOrCreate(
        //             ['stripe_transaction_id' => $invoice->payment_intent->id],
        //             [
        //                 'invoice_id' => $localInvoice->id,
        //                 'status'     => 'paid',
        //                 'paid_at'    => now(),
        //             ]
        //         );
        //         Log::info("✅ Transaction created with ID: {$trans->id}");
        //         // Mail::to('lionsubhan123@gmail.com')
        //         //     ->send(new TransactionPaidMail($invoice->subscription->user, $trans, true));
        //     }
        // });

        // 5️⃣ Send mail
    }
    private function onInvoicePaymentPaid($inv)
    {

        $invoice = StripeInvoice::retrieve([
            'id' => $inv->invoice,
            'expand' => ['payment_intent', 'charge', 'subscription'],
        ]);

        // Log::info("Invoice Subscription Succeeded : {$invoice->lines->data[0]->parent->subscription_item_details->subscription}");

        DB::transaction(function () use ($inv, $invoice) {


            $localInvoice = Invoice::where('stripe_invoice_id', $inv->invoice)->first();
            if ($inv->payment->payment_intent) {
                $trans = Transaction::updateOrCreate(
                    [
                        'stripe_transaction_id' => $inv->payment->payment_intent,
                        'invoice_id' => $localInvoice->id,
                        'status'     => 'paid',
                        'paid_at'    => now(),
                    ]
                );
                Log::info("✅ Transaction created with ID: {$trans->id}");
                // Mail::to(env('ADMIN_EMAIL'))
                //     ->send(new TransactionPaidMail($localInvoice->subscription->user, $trans, true));
            }
        });
    }

    private function onInvoicePaymentFailed($inv)
    {

        $invoice = StripeInvoice::retrieve([
            'id' => $inv->invoice,
            'expand' => ['payment_intent', 'charge', 'subscription'],
        ]);

        // Log::info("Invoice Subscription Succeeded : {$invoice->lines->data[0]->parent->subscription_item_details->subscription}");

        DB::transaction(function () use ($inv, $invoice) {
            $subscription = Subscription::where('stripe_subscription_id', $invoice->lines->data[0]->parent->subscription_item_details->subscription)->first();

            $localInvoice = Invoice::updateOrCreate(
                [
                    'stripe_invoice_id' => $inv->invoice,
                    'subscription_id' => $subscription->id,
                    'amount_due'      => $inv->amount_paid / 100,
                    'currency'        => $inv->currency,
                    'invoice_date'    => $this->ts($inv->created),
                ]
            );
            Mail::to(env('ADMIN_EMAIL'))
                ->send(new TransactionFailedMail($localInvoice->subscription->user, $inv->invoice, true));
            Mail::to($localInvoice->subscription->user->email)
                ->send(new TransactionFailedMail($localInvoice->subscription->user, $inv->invoice));
            $localInvoice = Invoice::where('stripe_invoice_id', $inv->invoice)->first();
            if ($inv->payment->payment_intent) {
                $trans = Transaction::updateOrCreate(
                    [
                        'stripe_transaction_id' => $inv->payment->payment_intent,
                        'invoice_id' => $localInvoice->id,
                        'status'     => 'failed',
                    ]
                );
                Log::info("✅ Transaction created with ID: {$trans->id}");
                // Mail::to(env('ADMIN_EMAIL'))
                //     ->send(new TransactionFailedMail($localInvoice->subscription->user, $trans, true));
            }
        });
    }
    private function onChargeSucceeded($event)
    {
        //
    }
    /* ---------- PAYMENT EVENTS ---------- */

    private function onPaymentIntentSucceeded($pi)
    {
        if (!empty($pi->invoice)) return; // already handled
        // Handle one-time donations here if needed
    }

    private function onChargeRefunded($charge)
    {
        //
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
