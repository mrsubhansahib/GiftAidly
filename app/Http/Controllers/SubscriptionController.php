<?php

namespace App\Http\Controllers;

use App\Notifications\UserActionNotification;
use App\Mail\InvoicePaidMail;
use App\Mail\SubscriptionScheduledMail;
use App\Mail\TransactionPaidMail;
use App\Models\Invoice;
use App\Models\ProductCatalog;
use App\Models\SpecialDonation;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionStartedMail;
use App\Models\Subscription;
use App\Models\User;
use Stripe\Stripe as StripeStripe;
use Illuminate\Support\Str;


class SubscriptionController extends Controller
{
    public function donateDailyWeeklyMonthly(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:gbp,usd,eur',
            'type' => 'required|in:day,week,month',
            'gift_aid' => 'nullable|in:yes,no',
            'address' => 'required_if:gift_aid,yes|max:500',
            'start_date' => 'required|date|after_or_equal:' . now()->toDateString(),
            'cancellation' => 'required|date|after:start_date',
            'stripeToken' => 'required|string',
            'charge_now' => 'nullable|boolean',
        ]);
        // dd($request->all());

        DB::beginTransaction();
        try {
            $invoice = null;

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_customer_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_customer_id' => $customer->id, 'address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
                auth()->user()->update(['address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address]);
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
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date, $tz);
            $end   = Carbon::createFromFormat('Y-m-d', $request->cancellation, $tz);
            // dd($start->toDateTimeString(), $end->toDateTimeString());
            $days   = (int) $start->diffInDays($end);
            $weeks  = (int) $start->diffInWeeks($end);
            $months = (int) $start->diffInMonths($end);
            $startIsFuture  = $start->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $start->copy();

            $iterationsDay   = $days + 1;
            $iterationsWeek  = $weeks + 1;
            $iterationsMonth = max(1, ($months ?: 0) + 1);
            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($iterationsDay),
                'week'  => $anchor->copy()->addWeeks($iterationsWeek),
                'month' => $anchor->copy()->addMonths($iterationsMonth),
                default => $anchor->copy()->addMonthsNoOverflow($iterationsMonth),
            };

            // dd($endDate->toDateString(),$start);

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
            // dd($endDate->toDateString());
            // Save local record
            $subscription = auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'gift_aid' => $request->gift_aid == "yes" ? $request->gift_aid : 'no',
                'start_date' => $subscription->current_period_start
                    ? Carbon::createFromTimestamp($subscription->current_period_start)
                    : $start,  // fallback to requested start_date
                'end_date'   => $request->type == 'day' ? $endDate->copy()->subDay()->subSecond() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)->subSecond()) : ($request->type == 'month' ? $endDate->copy()->subMonth()->subSecond() : $endDate->copy()->subSecond())),
                'canceled_at' => $request->type == 'day' ? $endDate->subDay() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)) : ($request->type == 'month' ? $endDate->copy()->subMonth() : $endDate)),
            ]);
            DB::commit();
            $adminEmail = env('ADMIN_EMAIL');
            DB::afterCommit(function () use ($subscription, $startIsFuture, $forceChargeNow, $request, $adminEmail) {
                // ---------------- COMMON VARIABLES ----------------
                $currencySymbols = [
                    'usd' => '$',
                    'gbp' => '£',
                    'eur' => '€',
                ];
                $currencySymbol = $currencySymbols[strtolower($request->currency)] ?? strtoupper($request->currency);

                $typeReadable = match (strtolower($request->type)) {
                    'day'   => 'Daily',
                    'week'  => 'Weekly',
                    'month' => 'Monthly',
                    'friday' => 'Friday',
                    default => ucfirst($request->type),
                };
                $admin = User::where('role', 'admin')->first();
                $userName = \Illuminate\Support\Str::title(auth()->user()->name);
                // ---------------- EMAILS ----------------
                if ($startIsFuture && !$forceChargeNow) {
                    $adminTitle = "🗓️ {$typeReadable} Donation Scheduled";
                    $adminMessage = "{$userName} has scheduled a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    $userTitle = "📅 {$typeReadable} Donation Scheduled";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has been scheduled successfully.";
                    Mail::to(auth()->user()->email)
                        ->send(new SubscriptionScheduledMail(auth()->user(), $subscription));
                    // Future → Scheduled
                    Mail::to($adminEmail)
                        ->send(new SubscriptionScheduledMail(auth()->user(), $subscription, true));
                } else {

                    // Immediate → Received
                    $adminTitle = "💰 New {$typeReadable} Donation Received";
                    $adminMessage = "{$userName} has started a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    // Immediate → Started
                    $userTitle = "💝 {$typeReadable} Donation Started";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has started successfully.";
                    Mail::to(auth()->user()->email)
                        ->send(new SubscriptionStartedMail(auth()->user(), $subscription));
                    Mail::to($adminEmail)
                        ->send(new SubscriptionStartedMail(auth()->user(), $subscription, true));
                }

                auth()->user()->notify(new UserActionNotification(
                    $userTitle,
                    $userMessage,
                    'user'
                ));
                $admin->notify(new UserActionNotification(
                    $adminTitle,
                    $adminMessage,
                    'admin'
                ));
            });

            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';
            return redirect('index')->with('success', $msg);
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
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:gbp,usd,eur',
            'gift_aid' => 'nullable|in:yes,no',
            'address' => 'required_if:gift_aid,yes|max:500',
            'start_date' => 'required|date|after_or_equal:' . now()->toDateString(),
            'cancellation' => 'required|date|after:start_date',
            'stripeToken' => 'required|string',
            'charge_now' => 'nullable|boolean',
        ]);

        // dd($request->all());

        DB::beginTransaction();
        try {

            $invoice = null;
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_customer_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_customer_id' => $customer->id, 'address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
                auth()->user()->update(['address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address]);
            }


            if (!ProductCatalog::where('name', 'Friday Donation')->exists()) {
                // Create Product in Stripe and save to local DB if not exists
                $product = Stripe\Product::create([
                    'name' => 'Friday Donation',
                ]);
                ProductCatalog::firstOrCreate([
                    'name' => 'Friday Donation',
                    'product_id' => $product->id,
                ]);
            } else {
                $product = Stripe\Product::retrieve(ProductCatalog::where('name', 'Friday Donation')->first()->product_id);
            }

            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency' => $request->currency,
                'recurring' => ['interval' => 'week'],
                'product' => $product->id,
            ]);

            // Window & dates
            $tz    = config('app.timezone');
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date, $tz);
            $end   = Carbon::createFromFormat('Y-m-d', $request->cancellation, $tz);


            $weeks  = (int) $start->diffInWeeks($end);
            $startIsFuture  = $start->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $start->copy();

            $iterationsWeek  = $weeks + 1;
            $endDate =  $anchor->copy()->addWeeks($iterationsWeek);

            // dd($start->toDateString(), $endDate->toDateString());

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
            $subscription = auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => 'friday',
                'gift_aid' => $request->gift_aid == "yes" ? $request->gift_aid : 'no',
                'start_date' => $subscription->current_period_start
                    ? Carbon::createFromTimestamp($subscription->current_period_start)
                    : $start,  // fallback to requested start_date
                'end_date'   => $endDate->copy()->subDays(7)->subSecond(),
                'canceled_at' => $endDate->copy()->subDays(7),
            ]);


            DB::commit();
            DB::afterCommit(function () use ($subscription, $startIsFuture, $forceChargeNow, $request) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();

                // 🔹 Currency symbols
                $currencySymbols = [
                    'usd' => '$',
                    'gbp' => '£',
                    'eur' => '€',
                ];
                $currencySymbol = $currencySymbols[strtolower($request->currency)] ?? strtoupper($request->currency);
                $typeReadable = 'Friday';
                $userName = Str::title(auth()->user()->name);

                /*
    |--------------------------------------------------------------------------
    | 🧍 USER NOTIFICATION
    |--------------------------------------------------------------------------
    */
                if ($startIsFuture && !$forceChargeNow) {
                    // Donation scheduled for later date

                    $adminTitle = "🗓️ {$typeReadable} Donation Scheduled";
                    $adminMessage = "{$userName} has scheduled a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    $userTitle = "📅 {$typeReadable} Donation Scheduled";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has been scheduled successfully.";
                    Mail::to(auth()->user()->email)
                        ->send(new SubscriptionScheduledMail(auth()->user(), $subscription));
                    // Future → Scheduled
                    Mail::to($adminEmail)
                        ->send(new SubscriptionScheduledMail(auth()->user(), $subscription, true));
                } else {

                    // Immediate → Received
                    $adminTitle = "💰 New {$typeReadable} Donation Received";
                    $adminMessage = "{$userName} has started a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    // Immediate → Started
                    $userTitle = "💝 {$typeReadable} Donation Started";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has started successfully.";
                    Mail::to(auth()->user()->email)
                        ->send(new SubscriptionStartedMail(auth()->user(), $subscription));
                    Mail::to($adminEmail)
                        ->send(new SubscriptionStartedMail(auth()->user(), $subscription, true));
                }

                auth()->user()->notify(new UserActionNotification(
                    $userTitle,
                    $userMessage,
                    'user'
                ));
                $admin->notify(new UserActionNotification(
                    $adminTitle,
                    $adminMessage,
                    'admin'
                ));
            });


            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';
            // ider notification send horaha ha subhan bhai
            auth()->user()->notify(new UserActionNotification(
                "Friday Donation",
                "Your Friday donation of {$request->amount} {$request->currency} has been scheduled."
            ));

            return redirect('index')->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function donateSpecial(Request $request)
    {
        $request->validate([
            'special' => 'required|exists:special_donations,id',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|in:GBP,USD,EUR',
            'gift_aid' => 'nullable|in:yes,no',
            'address' => 'required_if:gift_aid,yes|max:500',
            'stripeToken' => 'required|string',
        ]);
        // dd($request->all());

        DB::beginTransaction();
        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // ✅ Ensure customer exists
            if (!auth()->user()->stripe_customer_id) {
                $customer = Stripe\Customer::create([
                    'name'  => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update([
                    'stripe_customer_id' => $customer->id,
                    'address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address
                ]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
                auth()->user()->update([
                    'address' => $request->gift_aid == "yes" ? $request->address : auth()->user()->address
                ]);
            }

            // ✅ Get selected special donation
            $donation = SpecialDonation::findOrFail($request->special);

            // ✅ Create/Retrieve product in Stripe
            if (!ProductCatalog::where('name', $donation->name)->exists()) {
                $product = Stripe\Product::create([
                    'name' => $donation->name,
                ]);
                ProductCatalog::firstOrCreate([
                    'name' => $donation->name,
                    'product_id' => $product->id,
                ]);
            } else {
                $product = Stripe\Product::retrieve(
                    ProductCatalog::where('name', $donation->name)->first()->product_id
                );
            }
            // ✅ Create one-time Price (no recurring)
            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency'    => $request->currency,
                'product'     => $product->id,
            ]);

            // dd($price);
            // ✅ Create PaymentIntent (one-time charge)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $request->amount * 100,
                'currency' => $request->currency,
                'customer' => $customer->id,
                'confirm'  => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // 🚀 force card-only, no redirects
                ],
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'token' => $request->stripeToken,
                    ],
                ],
            ]);

            // dd($paymentIntent);
            $subscription = auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => 'one-time-' . $paymentIntent->id,
                'stripe_price_id' => $price->id,
                'status' => 'ended',
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => 'special (' . $donation->name . ')',
                'gift_aid' => $request->gift_aid == "yes" ? $request->gift_aid : 'no',
                'start_date' => now(),  // fallback to requested start_date
                'end_date'   => now()->addSecond(),
                'canceled_at' => now()->addSeconds(2),
            ]);

            // ✅ Save local invoice
            $invoice = Invoice::create([
                'subscription_id'   => $subscription->id, // one-time, no subscription
                'stripe_invoice_id' => $paymentIntent->id, // store PaymentIntent ID instead
                'amount_due'        => $request->amount,
                'currency'          => $request->currency,
                'invoice_date'      => now(),
                'paid_at'           => now(),
            ]);

            // ✅ Save local transaction
            $transaction = Transaction::create([
                'invoice_id'            => $invoice->id,
                'stripe_transaction_id' => $paymentIntent->charges->data[0]->id ?? $paymentIntent->id,
                'paid_at'               => now(),
                'status'                => 'paid',
            ]);

            DB::commit();
            DB::afterCommit(function () use ($subscription, $invoice, $transaction) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();
                // 🧠 Currency symbol mapping
                $currencySymbols = [
                    'usd' => '$',
                    'gbp' => '£',
                    'eur' => '€',
                ];
                $currencySymbol = $currencySymbols[strtolower($subscription->currency)] ?? strtoupper($subscription->currency);

                // 📝 Type readable
                $typeReadable = 'Special Donation';

                // 🧍 Proper user name
                $userName = \Illuminate\Support\Str::title(auth()->user()->name);

                // 📢 1) USER Notification
                $adminTitle = "💰 New {$typeReadable} Received";
                $adminMessage = "{$userName} donated {$currencySymbol}{$subscription->price} towards {$subscription->type}.";
                $userTitle = "💝 {$typeReadable} Successful";
                $userMessage = "You donated {$currencySymbol}{$subscription->price} towards {$subscription->type}.";
                auth()->user()->notify(new UserActionNotification(
                    $userTitle,
                    $userMessage,
                    'user'
                ));
                $admin->notify(new UserActionNotification(
                    $adminTitle,
                    $adminMessage,
                    'admin'
                ));
                // ✅ ADMIN MAILS
                Mail::to($adminEmail)
                    ->send(new SubscriptionStartedMail(auth()->user(), $subscription, true));
                Mail::to($adminEmail)
                    ->send(new InvoicePaidMail(auth()->user(), $invoice, true));
                Mail::to($adminEmail)
                    ->send(new TransactionPaidMail(auth()->user(), $transaction, true));
                Mail::to(auth()->user()->email)
                    ->send(new SubscriptionStartedMail(auth()->user(), $subscription));

                Mail::to(auth()->user()->email)
                    ->send(new InvoicePaidMail(auth()->user(), $invoice));
            });


            return redirect('index')->with('success', 'Special donation successful! Invoice finalized & paid immediately.');
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function cancelSubscription($id)
    {
        $subscription = Subscription::where('id', $id)->first();
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            // 2️⃣ Cancel in Stripe
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripeSub = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
            $stripeSub->cancel();

            // 3️⃣ Prepare Notification Data
            $currencySymbols = [
                'usd' => '$',
                'gbp' => '£',
                'eur' => '€',
            ];
            $currencySymbol = $currencySymbols[strtolower($subscription->currency)] ?? strtoupper($subscription->currency);

            $typeReadable = match ($subscription->type) {
                'day'    => 'Daily',
                'week'   => 'Weekly',
                'month'  => 'Monthly',
                'friday' => 'Friday',
                default  => ucfirst($subscription->type),
            };

            $userName = \Illuminate\Support\Str::title($subscription->user->name ?? 'User');
            $amount = $subscription->price;

            // 4️⃣ Notifications after commit
            DB::afterCommit(function () use ($subscription, $userName, $typeReadable, $currencySymbol, $amount) {

                $admin = \App\Models\User::where('role', 'admin')->first();

                // 🧍 USER Notification
                $userTitle = "🚫 {$typeReadable} Donation Canceled";
                $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$amount} has been canceled successfully.";

                $subscription->user?->notify(new UserActionNotification(
                    $userTitle,
                    $userMessage,
                    'user'
                ));

                // 🧑‍💼 ADMIN Notification
                if ($admin) {
                    $adminTitle = "❌ {$typeReadable} Donation Canceled";
                    $adminMessage = "{$userName} has canceled their {$typeReadable} donation of {$currencySymbol}{$amount}.";

                    $admin->notify(new UserActionNotification(
                        $adminTitle,
                        $adminMessage,
                        'admin'
                    ));
                }
            });

            return redirect()->back()->with('success', 'Subscription canceled successfully');
        }
    }
}
