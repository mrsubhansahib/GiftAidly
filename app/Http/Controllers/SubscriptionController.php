<?php

namespace App\Http\Controllers;

use App\Models\ProductCatalog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe;

class SubscriptionController extends Controller
{
    public function donateDailyWeeklyMonthly(Request $request)
    {

        // dd($request->all());

        DB::beginTransaction();
        try {


            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_customer_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->name ,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_customer_id' => $customer->id, 'address' => $request->gift_aid=="yes" ? $request->address : auth()->user()->address]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
                auth()->user()->update(['address' => $request->gift_aid=="yes" ? $request->address : auth()->user()->address]);
            }

            if ($request->type == 'day') {
                if (!ProductCatalog::where('name', 'Daily Donation')->exists()) {
                    // Create Product in Stripe and save to local DB if not exists         
                    $product = Stripe\Product::create([
                        'name' => 'Daily Donation',
                    ]);
                    ProductCatalog::firstOrCreate([
                        'name' => 'Daily Donation',
                        'product_id' => $product->id,
                    ]);
                } else {
                    $product = Stripe\Product::retrieve(ProductCatalog::where('name', 'Daily Donation')->first()->product_id);
                }
            } else if ($request->type == 'week') {
                if (!ProductCatalog::where('name', 'Weekly Donation')->exists()) {
                    // Create Product in Stripe and save to local DB if not exists
                    $product = Stripe\Product::create([
                        'name' => 'Weekly Donation',
                    ]);
                    ProductCatalog::firstOrCreate([
                        'name' => 'Weekly Donation',
                        'product_id' => $product->id,
                    ]);
                } else {
                    $product = Stripe\Product::retrieve(ProductCatalog::where('name', 'Weekly Donation')->first()->product_id);
                }
            } else {
                if (!ProductCatalog::where('name', 'Monthly Donation')->exists()) {
                    // Create Product in Stripe and save to local DB if not exists
                    $product = Stripe\Product::create([
                        'name' => 'Monthly Donation',
                    ]);
                    ProductCatalog::firstOrCreate([
                        'name' => 'Monthly Donation',
                        'product_id' => $product->id,
                    ]);
                } else {
                    $product = Stripe\Product::retrieve(ProductCatalog::where('name', 'Monthly Donation')->first()->product_id);
                }
            }
            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency' => $request->currency,
                'recurring' => ['interval' => $request->type],
                'product' => $product->id,
            ]);

            // Window & dates
            $tz    = config('app.timezone');
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date, $tz)->startOfDay();
            $end   = Carbon::createFromFormat('Y-m-d', $request->cancellation, $tz);

            $days   = (int) $start->diffInDays($end);
            $weeks  = (int) $start->diffInWeeks($end);
            $months = (int) $start->diffInMonths($end);
            $startIsFuture  = $start->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $start->copy();

            $iterationsDay   = $days + 1;
            $iterationsWeek  = $weeks + 1;
            $iterationsMonth = max(1, ($months ?: 0)) + 1;
            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($iterationsDay),
                'week'  => $anchor->copy()->addWeeks($iterationsWeek),
                'month' => $anchor->copy()->addMonths($iterationsMonth),
                default => $anchor->copy()->addMonthsNoOverflow($iterationsMonth),
            };

            // dd($endDate->toDateString());

            if ($forceChargeNow || !$startIsFuture) {
                // ===== IMMEDIATE-CHARGE PATH =====
                // IMPORTANT: Do NOT send billing_cycle_anchor (Stripe will start "now")
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete', // we'll finalize+pay below
                    'expand'             => ['latest_invoice'],
                ]);

                // Always fetch invoice by ID
                $latest   = $subscription->latest_invoice;
                $latestId = is_string($latest) ? $latest : ($latest->id ?? null);
                if (!$latestId) {
                    throw new \Exception('Latest invoice ID not found on subscription (immediate charge path).');
                }
                $invoice = Stripe\Invoice::retrieve($latestId);

                // Finalize if draft
                if ($invoice->status === 'draft') {
                    $invoice = $invoice->finalizeInvoice(); // instance method
                }

                // Pay now if not paid yet
                if ($invoice->collection_method === 'charge_automatically' && $invoice->status !== 'paid') {
                    $invoice = $invoice->pay(); // instance method
                }
            } else {
                // ===== FUTURE START / TRIAL PATH =====
                // No invoice yet; it will be created at trial_end
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'trial_end'          => $start->timestamp,   // start & bill on this date
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete',
                ]);
            }

            // Save local record
            auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'gift_aid' => $request->gift_aid=="yes" ? $request->gift_aid : 'no',
                'start_date' => $subscription->current_period_start
                    ? Carbon::createFromTimestamp($subscription->current_period_start)
                    : $start,  // fallback to requested start_date
                'end_date'   => $request->type == 'day' ? $endDate->copy()->subDay()->subSecond() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)->subSecond()) : ($request->type == 'month' ? $endDate->copy()->subMonth()->subSecond() : $endDate->copy()->subSecond())),
                'canceled_at' => $request->type == 'day' ? $endDate->subDay() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)) : ($request->type == 'month' ? $endDate->copy()->subMonth() : $endDate)),
            ]);
            DB::commit();

            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';

            return redirect()->back()->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function donateFriday(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();
        try {


            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_id' => $customer->id]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_id);
            }

            $product = Stripe\Product::create([
                'name' => 'Custom Subscription',
            ]);

            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency' => $request->currency,
                'recurring' => ['interval' => $request->type],
                'product' => $product->id,
            ]);

            // Window & dates
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $days   = Carbon::parse($request->cancellation)->diffInDays($request->start_date);
            $weeks  = Carbon::parse($request->cancellation)->diffInWeeks($request->start_date);
            $months = Carbon::parse($request->cancellation)->diffInMonths($request->start_date);

            $startIsFuture  = $startDate->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $startDate->copy();

            $iterationsDay   = $days + 1;
            $iterationsWeek  = $weeks + 1;
            $iterationsMonth = max(1, ($months ?: 0) + 1);

            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($iterationsDay),
                'week'  => $anchor->copy()->addWeeks($iterationsWeek),
                default => $anchor->copy()->addMonthsNoOverflow($iterationsMonth),
            };

            if ($forceChargeNow || !$startIsFuture) {
                // ===== IMMEDIATE-CHARGE PATH =====
                // IMPORTANT: Do NOT send billing_cycle_anchor (Stripe will start "now")
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete', // we'll finalize+pay below
                    'expand'             => ['latest_invoice'],
                ]);

                // Always fetch invoice by ID
                $latest   = $subscription->latest_invoice;
                $latestId = is_string($latest) ? $latest : ($latest->id ?? null);
                if (!$latestId) {
                    throw new \Exception('Latest invoice ID not found on subscription (immediate charge path).');
                }
                $invoice = Stripe\Invoice::retrieve($latestId);

                // Finalize if draft
                if ($invoice->status === 'draft') {
                    $invoice = $invoice->finalizeInvoice(); // instance method
                }

                // Pay now if not paid yet
                if ($invoice->collection_method === 'charge_automatically' && $invoice->status !== 'paid') {
                    $invoice = $invoice->pay(); // instance method
                }
            } else {
                // ===== FUTURE START / TRIAL PATH =====
                // No invoice yet; it will be created at trial_end
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'trial_end'          => $startDate->timestamp,   // start & bill on this date
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete',
                ]);
            }

            // Save local record
            auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date'   => $endDate->copy()->subSecond(),
                'canceled_at' => $endDate,
            ]);
            DB::commit();

            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';

            return redirect()->route('dashboard')->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function donateMonthly(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();
        try {


            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_id' => $customer->id]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_id);
            }

            $product = Stripe\Product::create([
                'name' => 'Custom Subscription',
            ]);

            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency' => $request->currency,
                'recurring' => ['interval' => $request->type],
                'product' => $product->id,
            ]);

            // Window & dates
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $days   = Carbon::parse($request->cancellation)->diffInDays($request->start_date);
            $weeks  = Carbon::parse($request->cancellation)->diffInWeeks($request->start_date);
            $months = Carbon::parse($request->cancellation)->diffInMonths($request->start_date);

            $startIsFuture  = $startDate->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $startDate->copy();

            $iterationsDay   = $days + 1;
            $iterationsWeek  = $weeks + 1;
            $iterationsMonth = max(1, ($months ?: 0) + 1);

            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($iterationsDay),
                'week'  => $anchor->copy()->addWeeks($iterationsWeek),
                default => $anchor->copy()->addMonthsNoOverflow($iterationsMonth),
            };

            if ($forceChargeNow || !$startIsFuture) {
                // ===== IMMEDIATE-CHARGE PATH =====
                // IMPORTANT: Do NOT send billing_cycle_anchor (Stripe will start "now")
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete', // we'll finalize+pay below
                    'expand'             => ['latest_invoice'],
                ]);

                // Always fetch invoice by ID
                $latest   = $subscription->latest_invoice;
                $latestId = is_string($latest) ? $latest : ($latest->id ?? null);
                if (!$latestId) {
                    throw new \Exception('Latest invoice ID not found on subscription (immediate charge path).');
                }
                $invoice = Stripe\Invoice::retrieve($latestId);

                // Finalize if draft
                if ($invoice->status === 'draft') {
                    $invoice = $invoice->finalizeInvoice(); // instance method
                }

                // Pay now if not paid yet
                if ($invoice->collection_method === 'charge_automatically' && $invoice->status !== 'paid') {
                    $invoice = $invoice->pay(); // instance method
                }
            } else {
                // ===== FUTURE START / TRIAL PATH =====
                // No invoice yet; it will be created at trial_end
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'trial_end'          => $startDate->timestamp,   // start & bill on this date
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete',
                ]);
            }

            // Save local record
            auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date'   => $endDate->copy()->subSecond(),
                'canceled_at' => $endDate,
            ]);
            DB::commit();

            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';

            return redirect()->route('dashboard')->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
