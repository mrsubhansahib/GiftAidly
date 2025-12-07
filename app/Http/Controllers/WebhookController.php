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
use App\Notifications\UserActionNotification;
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
                    $pi = $payload['data']['object'];
                    $this->handleZakatPayment($pi);
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
        //
    }

    private function onSubscriptionDeleted($sub)
    {
        DB::transaction(function () use ($sub) {
            try {
                $subscription = Subscription::where('stripe_subscription_id', $sub->id)->first();
                if (!$subscription) {
                    Log::warning("Subscription with Stripe ID {$sub->id} not found.");
                    return;
                }

                $status = 'ended';
                $isEarlyCancel = $subscription->end_date && $subscription->end_date->isFuture();

                if ($isEarlyCancel) {
                    $status = 'canceled';
                }

                $subscription->update([
                    'status' => $status,
                    'canceled_at' => now(),
                ]);

                Log::info("Subscription {$sub->id} marked as {$status}.");

                DB::afterCommit(function () use ($subscription, $status) {
                    $currencySymbols = ['usd' => '$', 'gbp' => '£', 'eur' => '€'];
                    $currencySymbol = $currencySymbols[strtolower($subscription->currency)] ?? strtoupper($subscription->currency);

                    $typeReadable = match ($subscription->type) {
                        'day'    => 'Daily',
                        'month'  => 'Monthly',
                        'week'   => 'Weekly',
                        'friday' => 'Friday',
                        default  => ucfirst($subscription->type),
                    };

                    $userName = \Illuminate\Support\Str::title($subscription->user->name ?? 'User');
                    $amount = $subscription->price;
                    $admin = User::where('role', 'admin')->first();

                    $statusLabel = $status === 'ended' ? 'Ended' : 'Canceled';
                    $userTitle = "🚫 {$typeReadable} Donation {$statusLabel}";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$amount} has {$statusLabel} successfully.";
                    $adminTitle = "❌ {$typeReadable} Donation {$statusLabel}";
                    $adminMessage = "{$userName}'s {$typeReadable} donation of {$currencySymbol}{$amount} has {$statusLabel}.";

                    $subscription->user?->notify(new UserActionNotification($userTitle, $userMessage, 'user'));
                    $admin?->notify(new UserActionNotification($adminTitle, $adminMessage, 'admin'));

                    Mail::to(env('ADMIN_EMAIL'))->send(new SubscriptionCanceledMail($subscription, true));
                    Mail::to($subscription->user->email)->send(new SubscriptionCanceledMail($subscription));
                });
            } catch (\Exception $e) {
                Log::error("Failed to update subscription {$sub->id}: " . $e->getMessage());
            }
        });
    }


    /* ---------- INVOICE EVENTS ---------- */


    private function onInvoiceCreated($inv)
    {
        DB::transaction(function () use ($inv) {
            try {
                if (($inv->amount_paid / 100) > 0) {
                    $invoice = StripeInvoice::retrieve([
                        'id' => $inv->id,
                        'expand' => ['payment_intent', 'charge', 'subscription'],
                    ]);
                    $subscription = Subscription::where('stripe_subscription_id', $invoice->lines->data[0]->parent->subscription_item_details->subscription)->first();
                    Invoice::updateOrCreate(
                        [
                            'stripe_invoice_id' => $inv->id,
                            'subscription_id' => $subscription->id,
                            'amount_due'      => $inv->amount_paid / 100,
                            'currency'        => $inv->currency,
                            'paid_at'        => $this->ts($inv->status_transitions->paid_at ?? now()),
                            'invoice_date'    => $this->ts($inv->created),
                        ]
                    );
                } else {
                    Log::info("Invoice with 0 amount created: {$inv->id}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to create/update invoice {$inv->id}: " . $e->getMessage());
            }
        });
    }
    private function onInvoicePaymentSucceeded($inv)
    {
        //
    }
    private function onInvoicePaymentPaid($inv)
    {

        $invoice = StripeInvoice::retrieve([
            'id' => $inv->invoice,
            'expand' => ['payment_intent', 'charge', 'subscription'],
        ]);

        // Log::info("Invoice Subscription Succeeded : {$invoice->lines->data[0]->parent->subscription_item_details->subscription}");

        DB::transaction(function () use ($inv, $invoice) {
            $subscription = Subscription::where('stripe_subscription_id', $invoice->lines->data[0]->parent->subscription_item_details->subscription)->first();
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
                if ($subscription->end_date && !$subscription->end_date->isFuture()) {
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripeSub = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                    $stripeSub->cancel();
                    Log::info("✅ Subscription canceled due to last invoice payment: {$subscription->stripe_subscription_id}");
                } else {
                    Log::info("✅ Subscription is still active: {$subscription->stripe_subscription_id}");
                }
                Log::info("✅ Transaction created with ID: {$trans->id}");
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
            $localInvoice = Invoice::where('stripe_invoice_id', $inv->invoice)->first();
            if ($inv->payment->payment_intent) {
                $trans = Transaction::updateOrCreate(
                    [
                        'stripe_transaction_id' => $inv->payment->payment_intent,
                        'invoice_id' => $localInvoice->id,
                        'status'     => 'failed',
                    ]
                );
                if ($subscription->end_date && !$subscription->end_date->isFuture()) {
                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $stripeSub = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                    $stripeSub->cancel();
                    Log::info("✅ Subscription canceled due to last invoice payment: {$subscription->stripe_subscription_id}");
                } else {
                    Log::info("✅ Subscription is still active: {$subscription->stripe_subscription_id}");
                }
                Log::info("✅Failed Transaction created with ID: {$trans->id}");
            }
        });
    }
    private function onChargeSucceeded($event)
    {
        //
    }
    /* ---------- PAYMENT EVENTS ---------- */

    private function handleZakatPayment($pi)
    {
        Log::info("Webhook: Processing Zakat PaymentIntent {$pi->id}");

        DB::transaction(function () use ($pi) {

            // -------------------------------------------
            // 1️⃣ GET STRIPE CUSTOMER → LOCAL USER
            // -------------------------------------------
            $customerId = $pi->customer ?? null;

            if (!$customerId) {
                Log::error("PaymentIntent {$pi->id} has NO CUSTOMER. Cannot attach to user.");
                return;
            }

            $user = User::where('stripe_customer_id', $customerId)->first();

            if (!$user) {
                Log::error("NO LOCAL USER FOUND for Stripe customer {$customerId}");
                return;
            }

            // -------------------------------------------
            // 2️⃣ GET AMOUNT / CURRENCY
            // -------------------------------------------
            $amount = ($pi->amount_received ?? $pi->amount ?? 0) / 100;
            $currency = strtolower($pi->currency ?? 'gbp');

            // -------------------------------------------
            // 3️⃣ DETERMINE TYPE (fallbacks)
            // -------------------------------------------
            $type = $pi->metadata->type
                ?? $pi->description
                ?? 'zakat';

            // Fallback gift aid
            $giftAid = $pi->metadata->gift_aid ?? 'no';

            // -------------------------------------------
            // 4️⃣ CREATE LOCAL SUBSCRIPTION (ONE-TIME MARKER)
            // -------------------------------------------
            $subscription = $user->subscriptions()->create([
                'stripe_subscription_id' => 'one-time-' . $pi->id,
                'stripe_price_id'        => $pi->metadata->price_id ?? 'n/a',
                'status'                 => 'ended',
                'price'                  => $amount,
                'currency'               => $currency,
                'type'                   => $type,
                'gift_aid'               => 'no',
                'start_date'             => now(),
                'end_date'               => now(),
                'canceled_at'            => now(),
            ]);

            // -------------------------------------------
            // 5️⃣ CREATE INVOICE
            // -------------------------------------------
            $invoice = Invoice::create([
                'subscription_id'   => $subscription->id,
                'stripe_invoice_id' => $pi->id,
                'amount_due'        => $amount,
                'currency'          => $currency,
                'invoice_date'      => now(),
                'paid_at'           => now(),
            ]);

            // -------------------------------------------
            // 6️⃣ CREATE TRANSACTION
            // -------------------------------------------
            $chargeId = $pi->latest_charge;

            Transaction::create([
                'invoice_id'            => $invoice->id,
                'stripe_transaction_id' => $chargeId,
                'paid_at'               => now(),
                'status'                => 'paid',
            ]);

            Log::info("Webhook: One-time donation saved for user {$user->email}");
        });
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
