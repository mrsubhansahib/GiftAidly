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
use App\Mail\ZakatMail;
use App\Models\Subscription;
use App\Models\User;
use App\Rules\HasValidMx;
use Exception;
use Illuminate\Support\Facades\Hash;
use Stripe\Stripe as StripeStripe;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;

class SubscriptionController extends Controller
{
    public function donateDailyWeeklyMonthly(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email' => ['required', 'string', 'email:rfc', 'max:255', new HasValidMx],
            'amount'       => 'required|numeric|min:1',
            'currency'     => 'required|in:gbp,usd,eur',
            'type'         => 'required|in:day,week,month',
            'gift_aid'     => 'nullable|in:yes,no',
            'address'      => 'required_if:gift_aid,yes|max:500',
            'start_date'   => 'required|date|after_or_equal:' . now()->toDateString(),
            'cancellation' => 'required|date|after:start_date',
            'stripeToken'  => 'required|string',
            'charge_now'   => 'nullable|boolean',
        ]);
        DB::beginTransaction();

        try {
            // ✅ 1. Create or find user
            $user = User::where('email', $request->email)->first();
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!$user) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('12345678'), // temp random password
                    'address'  => $request->gift_aid === 'yes' ? $request->address : null,
                    'role'     => 'donor',
                ]);
                $customer = Stripe\Customer::create([
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'source' => $request->stripeToken,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                // user exists → optional: update address only if new gift aid entered
                if ($request->gift_aid === 'yes' && $request->filled('address')) {
                    $user->update(['address' => $request->address]);
                }
                if ($user->stripe_customer_id) {
                    $customer = Stripe\Customer::retrieve($user->stripe_customer_id);
                    // ✅ Attach new card if a new token was sent
                    if ($request->filled('stripeToken')) {
                        $newSource = $customer->sources->create(['source' => $request->stripeToken]);
                        $customer->default_source = $newSource->id;
                        $customer->save();
                    }
                }
            }
            // ✅ 4. Get or create product in Stripe (fixed version)
            $typeName = match ($request->type) {
                'day'   => 'Daily Donation',
                'week'  => 'Weekly Donation',
                'month' => 'Monthly Donation',
            };
            $product = null;
            if (!ProductCatalog::where('name', $typeName)->exists()) {
                $product = Stripe\Product::create(['name' => $typeName]);
                ProductCatalog::create([
                    'name' => $typeName,
                    'product_id' => $product->id,
                ]);
            } else {
                $product = Stripe\Product::retrieve(ProductCatalog::where('name', $typeName)->first()->product_id);
            }
            // ✅ 5. Create Stripe Price
            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency'    => $request->currency,
                'recurring'   => ['interval' => $request->type],
                'product'     => $product->id,
            ]);

            // ✅ 6. Date calculations
            $tz    = config('app.timezone');
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date, $tz);
            $end   = Carbon::createFromFormat('Y-m-d', $request->cancellation, $tz);

            $startIsFuture  = $start->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $start->copy();

            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($start->diffInDays($end) + 1),
                'week'  => $anchor->copy()->addWeeks($start->diffInWeeks($end) + 1),
                'month' => $anchor->copy()->addMonths(max(1, ($start->diffInMonths($end) ?: 0) + 1)),
            };

            // ✅ 7. Create subscription

            $data = [
                'customer'           => $customer->id,
                'items'              => [['price' => $price->id]],
                'cancel_at'          => $endDate->timestamp,
                'proration_behavior' => 'none',
                'collection_method'  => 'charge_automatically',
                'payment_behavior'   => 'allow_incomplete',
            ];
            // Add extra fields conditionally
            if ($forceChargeNow || !$startIsFuture) {
                // Immediate charge
                $data['expand'] = ['latest_invoice'];
            } else {
                // Future start (trial period)
                $data['trial_end'] = $start->timestamp;
            }

            // Now pass the final array
            $subscription = \Stripe\Subscription::create($data);

            // ✅ 8. Save subscription locally
            $localSubscription = $user->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id'        => $price->id,
                'status'                 => $subscription->status,
                'price'                  => $request->amount,
                'currency'               => $request->currency,
                'type'                   => $request->type,
                'gift_aid'               => $request->gift_aid === 'yes' ? 'yes' : 'no',
                'start_date'             => $subscription->current_period_start
                    ? Carbon::createFromTimestamp($subscription->current_period_start)
                    : $start,
                'end_date'   => $request->type == 'day' ? $endDate->copy()->subDay()->subSecond() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)->subSecond()) : ($request->type == 'month' ? $endDate->copy()->subMonth()->subSecond() : $endDate->copy()->subSecond())),
                'canceled_at' => $request->type == 'day' ? $endDate->subDay() : ($request->type == 'week' ? ($endDate->copy()->subDays(7)) : ($request->type == 'month' ? $endDate->copy()->subMonth() : $endDate)),
            ]);

            DB::commit();

            // ✅ 9. Send Emails + Notifications (queued after commit)
            DB::afterCommit(function () use ($user, $localSubscription, $startIsFuture, $forceChargeNow, $request) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();

                $currencySymbols = ['usd' => '$', 'gbp' => '£', 'eur' => '€'];
                $currencySymbol = $currencySymbols[strtolower($request->currency)] ?? strtoupper($request->currency);
                $typeReadable = ucfirst($request->type);
                $userName = Str::title($user->name);

                if ($startIsFuture && !$forceChargeNow) {
                    Mail::to($user->email)->send(new SubscriptionScheduledMail($user, $localSubscription));
                    Mail::to($adminEmail)->send(new SubscriptionScheduledMail($user, $localSubscription, true));
                    $user->notify(new UserActionNotification("📅 {$typeReadable} Donation Scheduled", "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has been scheduled.", 'user'));
                    $admin?->notify(new UserActionNotification("🗓️ {$typeReadable} Donation Scheduled", "{$userName} has scheduled a {$typeReadable} donation of {$currencySymbol}{$request->amount}.", 'admin'));
                } else {
                    Mail::to($user->email)->send(new SubscriptionStartedMail($user, $localSubscription));
                    Mail::to($adminEmail)->send(new SubscriptionStartedMail($user, $localSubscription, true));
                    $user->notify(new UserActionNotification("💝 {$typeReadable} Donation Started", "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has started successfully.", 'user'));
                    $admin?->notify(new UserActionNotification("💰 New {$typeReadable} Donation Received", "{$userName} has started a {$typeReadable} donation of {$currencySymbol}{$request->amount}.", 'admin'));
                }
                // ✅ 10. Redirect with message
            });
            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';
            return redirect()->back()->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function donateFriday(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'         => 'required|string|max:255',
            'email' => ['required', 'string', 'email:rfc', 'max:255', new HasValidMx],
            'amount'       => 'required|numeric|min:1',
            'currency'     => 'required|in:gbp,usd,eur',
            'gift_aid'     => 'nullable|in:yes,no',
            'address'      => 'required_if:gift_aid,yes|max:500',
            'start_date'   => 'required|date|after_or_equal:' . now()->toDateString(),
            'cancellation' => 'required|date|after:start_date',
            'stripeToken'  => 'required|string',
            'charge_now'   => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            // ✅ 1. Find or create donor
            $user = User::where('email', $request->email)->first();

            // ✅ 2. Stripe setup
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer = null;
            if (!$user) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('12345678'),
                    'address'  => $request->gift_aid === 'yes' ? $request->address : null,
                    'role'     => 'donor',
                ]);
                $customer = Stripe\Customer::create([
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'source' => $request->stripeToken,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
                // $user->sendEmailVerificationNotification();
            } else {
                // user exists → optional: update address only if new gift aid entered
                if ($request->gift_aid === 'yes' && $request->filled('address')) {
                    $user->update(['address' => $request->address]);
                }
                if ($user->stripe_customer_id) {
                    $customer = Stripe\Customer::retrieve($user->stripe_customer_id);
                    // ✅ Attach new card if a new token was sent
                    if ($request->filled('stripeToken')) {
                        $newSource = $customer->sources->create(['source' => $request->stripeToken]);
                        $customer->default_source = $newSource->id;
                        $customer->save();
                    }
                }
            }


            // ✅ 3. Create or retrieve Stripe customer

            // ✅ 4. Create or get Friday product
            $stripeProduct = null;
            if (!ProductCatalog::where('name', 'Friday Donation')->exists()) {
                $stripeProduct = Stripe\Product::create(['name' => 'Friday Donation']);
                $productCatalog = ProductCatalog::create([
                    'name' => 'Friday Donation',
                    'product_id' => $stripeProduct->id,
                ]);
            } else {
                $stripeProduct = Stripe\Product::retrieve(ProductCatalog::where('name', 'Friday Donation')->first()->product_id);
            }

            // ✅ 5. Create recurring price (weekly)
            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency'    => $request->currency,
                'recurring'   => ['interval' => 'week'],
                'product'     => $stripeProduct->id,
            ]);
            // ✅ 6. Date handling
            $tz    = config('app.timezone');
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date, $tz);
            $end   = Carbon::createFromFormat('Y-m-d', $request->cancellation, $tz);

            $weeks         = $start->diffInWeeks($end);
            $startIsFuture = $start->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $start->copy();
            $endDate = $anchor->copy()->addWeeks($weeks + 1);


            $data = [
                'customer'           => $customer->id,
                'items'              => [['price' => $price->id]],
                'cancel_at'          => $endDate->timestamp,
                'proration_behavior' => 'none',
                'collection_method'  => 'charge_automatically',
                'payment_behavior'   => 'allow_incomplete',

            ];
            // ✅ 7. Create subscription
            if ($forceChargeNow || !$startIsFuture) {
                // Immediate charge
                $data['expand'] = ['latest_invoice'];
            } else {
                // Future start (trial period)
                $data['trial_end'] = $start->timestamp;
            }
            $subscription = \Stripe\Subscription::create($data);

            // ✅ 8. Save local record
            $localSubscription = $user->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id'        => $price->id,
                'status'                 => $subscription->status,
                'price'                  => $request->amount,
                'currency'               => $request->currency,
                'type'                   => 'friday',
                'gift_aid'               => $request->gift_aid === 'yes' ? 'yes' : 'no',
                'start_date'             => $subscription->current_period_start
                    ? Carbon::createFromTimestamp($subscription->current_period_start)
                    : $start,
                'end_date'   => $endDate->copy()->subDays(7)->subSecond(),
                'canceled_at' => $endDate->copy()->subDays(7),
            ]);

            DB::commit();

            // ✅ 9. After commit — same structure as daily/weekly/monthly
            DB::afterCommit(function () use ($user, $localSubscription, $startIsFuture, $forceChargeNow, $request) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();

                $currencySymbols = [
                    'usd' => '$',
                    'gbp' => '£',
                    'eur' => '€',
                ];
                $currencySymbol = $currencySymbols[strtolower($request->currency)] ?? strtoupper($request->currency);

                $typeReadable = 'Friday';
                $userName = Str::title($user->name);

                if ($startIsFuture && !$forceChargeNow) {
                    // 🔹 Scheduled donation (future start)
                    $adminTitle = "🗓️ {$typeReadable} Donation Scheduled";
                    $adminMessage = "{$userName} has scheduled a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    $userTitle = "📅 {$typeReadable} Donation Scheduled";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has been scheduled successfully.";
                    Mail::to($user->email)
                        ->send(new SubscriptionScheduledMail($user, $localSubscription));
                    Mail::to($adminEmail)
                        ->send(new SubscriptionScheduledMail($user, $localSubscription, true));
                } else {
                    // 🔹 Immediate donation (charged now)
                    $adminTitle = "💰 New {$typeReadable} Donation Received";
                    $adminMessage = "{$userName} has started a {$typeReadable} donation of {$currencySymbol}{$request->amount}.";
                    $userTitle = "💝 {$typeReadable} Donation Started";
                    $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$request->amount} has started successfully.";
                    Mail::to($user->email)
                        ->send(new SubscriptionStartedMail($user, $localSubscription));
                    Mail::to($adminEmail)
                        ->send(new SubscriptionStartedMail($user, $localSubscription, true));
                }
                // 🔹 Notifications (same format)
                $user->notify(new UserActionNotification(
                    $userTitle,
                    $userMessage,
                    'user'
                ));
                $admin?->notify(new UserActionNotification(
                    $adminTitle,
                    $adminMessage,
                    'admin'
                ));
            });

            // ✅ 10. Redirect message
            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';

            return redirect()->back()->with('success', $msg);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function donateSpecial(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email' => ['required', 'string', 'email:rfc', 'max:255', new HasValidMx],
            'special'   => 'required|exists:special_donations,id',
            'amount'    => 'required|numeric|min:1',
            'currency'  => 'required|in:GBP,USD,EUR',
            'gift_aid'  => 'nullable|in:yes,no',
            'address'   => 'required_if:gift_aid,yes|max:500',
            'stripeToken' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // ✅ 1. Create or find donor
            $user = User::where('email', $request->email)->first();

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            if (!$user) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('password'),
                    'address'  => $request->gift_aid === 'yes' ? $request->address : null,
                    'role'     => 'donor',
                ]);
                $customer = Stripe\Customer::create([
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'source' => $request->stripeToken,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                // user exists → optional: update address only if new gift aid entered
                if ($request->gift_aid === 'yes' && $request->filled('address')) {
                    $user->update(['address' => $request->address]);
                }
                if ($user->stripe_customer_id) {

                    $customer = Stripe\Customer::retrieve($user->stripe_customer_id);
                    // ✅ Attach new card if a new token was sent
                    if ($request->filled('stripeToken')) {
                        $newSource = $customer->sources->create(['source' => $request->stripeToken]);
                        $customer->default_source = $newSource->id;
                        $customer->save();
                    }
                }
            }

            // ✅ 4. Get selected special donation
            $donation = SpecialDonation::findOrFail($request->special);

            // ✅ 5. Create or get product in Stripe

            if (! ProductCatalog::where('name', $donation->name)->exists()) {
                $stripeProduct = Stripe\Product::create(['name' => $donation->name]);
                $productCatalog = ProductCatalog::create([
                    'name' => $donation->name,
                    'product_id' => $stripeProduct->id,
                ]);
            } else {
                $stripeProduct = Stripe\Product::retrieve(ProductCatalog::where('name', $donation->name)->first()->product_id);
            }

            // ✅ 6. Create one-time Price
            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency'    => strtolower($request->currency),
                'product'     => $stripeProduct->id,
            ]);

            // ✅ 7. Create PaymentIntent (one-time payment)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $request->amount * 100,
                'currency' => $request->currency,
                'customer' => $customer->id,
                'confirm'  => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'description' => 'Special Donation',
            ]);

            // ✅ 8. Save local subscription (one-time marker)
            $localSubscription = $user->subscriptions()->create([
                'stripe_subscription_id' => 'one-time-' . $paymentIntent->id,
                'stripe_price_id'        => $price->id,
                'status'                 => 'ended',
                'price'                  => $request->amount,
                'currency'               => strtolower($request->currency),
                'type'                   => 'special (' . $donation->name . ')',
                'gift_aid'               => $request->gift_aid === 'yes' ? 'yes' : 'no',
                'start_date'             => now(),
                'end_date'               => now()->addSecond(),
                'canceled_at'            => now()->addSeconds(2),
            ]);

            // ✅ 9. Create local invoice
            $invoice = Invoice::create([
                'subscription_id'   => $localSubscription->id,
                'stripe_invoice_id' => $paymentIntent->id,
                'amount_due'        => $request->amount,
                'currency'          => strtolower($request->currency),
                'invoice_date'      => now(),
                'paid_at'           => now(),
            ]);

            // ✅ 10. Create local transaction
            $transaction = Transaction::create([
                'invoice_id'            => $invoice->id,
                'stripe_transaction_id' => $paymentIntent->charges->data[0]->id ?? $paymentIntent->id,
                'paid_at'               => now(),
                'status'                => 'paid',
            ]);

            DB::commit();

            // ✅ 11. Notifications & Emails
            DB::afterCommit(function () use ($user, $localSubscription, $invoice, $transaction, $donation) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();

                $currencySymbols = ['usd' => '$', 'gbp' => '£', 'eur' => '€'];
                $currencySymbol = $currencySymbols[strtolower($localSubscription->currency)] ?? strtoupper($localSubscription->currency);
                $userName = Str::title($user->name);
                $typeReadable = 'Special Donation';

                // 🧍‍♂️ User + Admin Notifications
                $userTitle = "💝 {$typeReadable} Successful";
                $userMessage = "You donated {$currencySymbol}{$localSubscription->price} towards {$localSubscription->type}.";
                $adminTitle = "💰 New {$typeReadable} Received";
                $adminMessage = "{$userName} donated {$currencySymbol}{$localSubscription->price} towards {$localSubscription->type}.";

                $user->notify(new UserActionNotification($userTitle, $userMessage, 'user'));
                $admin?->notify(new UserActionNotification($adminTitle, $adminMessage, 'admin'));

                // 📨 Emails
                Mail::to($user->email)->send(new SubscriptionStartedMail($user, $localSubscription));
                Mail::to($adminEmail)->send(new SubscriptionStartedMail($user, $localSubscription, true));
            });

            return redirect()->back()->with('success', 'Special donation successful! Invoice finalized & paid immediately.');
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function cancelSubscription($id)
    {
        $subscription = Subscription::where('id', $id)->first();
        DB::beginTransaction();
        try {
            // 1️⃣ Update local record
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $stripeSub = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
            $stripeSub->cancel();
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
            // 3️⃣ Prepare Notification Data
            $currencySymbols = [
                'usd' => '$',
                'gbp' => '£',
                'eur' => '€',
            ];
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
            DB::commit();

            // 4️⃣ Notifications after commit
            DB::afterCommit(function () use ($subscription, $userName, $typeReadable, $currencySymbol, $amount) {
                $admin = User::where('role', 'admin')->first();
                // 🧍 USER Notification
                $userTitle = "🚫 {$typeReadable} Donation Canceled";
                $userMessage = "Your {$typeReadable} donation of {$currencySymbol}{$amount} has been canceled successfully.";
                $adminTitle = "❌ {$typeReadable} Donation Canceled";
                $adminMessage = "{$userName} has canceled their {$typeReadable} donation of {$currencySymbol}{$amount}.";

                $subscription->user?->notify(new UserActionNotification(
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
            return redirect()->back()->with('success', 'Subscription canceled successfully');
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error canceling subscription: ' . $e->getMessage());
        }
    }
    public function donateZakat(Request $request)
    {
        // 🔹 Normalize currency symbols and validate

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email:rfc', 'max:255', new HasValidMx],
            'currency' => 'required|in:gbp,usd,eur',
            'zakat' => 'required|numeric|min:1',
            'stripeToken' => 'required|string',
        ]);
        $stripe_token = $request->stripeToken;
        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            if (!$user) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('password'),
                    'role'     => 'donor',
                ]);
                $customer = Stripe\Customer::create([
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'source' => $stripe_token,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                if ($user->stripe_customer_id) {
                    $customer = Stripe\Customer::retrieve($user->stripe_customer_id);
                    // ✅ Attach new card if a new token was sent
                    if ($request->filled('stripeToken')) {
                        $newSource = $customer->sources->create(['source' => $request->stripeToken]);
                        $customer->default_source = $newSource->id;
                        $customer->save();
                    }
                }
            }


            // ✅ Create or retrieve Stripe Product for Zakat Donation
            $productName = 'Zakat';
            if (! ProductCatalog::where('name', $productName)->exists()) {
                $stripeProduct = Stripe\Product::create(['name' => $productName]);
                $productCatalog = ProductCatalog::create([
                    'name' => $productName,
                    'product_id' => $stripeProduct->id,
                ]);
            } else {
                $stripeProduct = Stripe\Product::retrieve(ProductCatalog::where('name', $productName)->first()->product_id);
            }
            // ✅ Create one-time Price
            $price = Stripe\Price::create([
                'unit_amount' => $request->zakat * 100,
                'currency'    => $request->currency,
                'product'     => $stripeProduct->id,
            ]);


            // ✅ 7. Create PaymentIntent (one-time payment)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $request->zakat * 100,
                'currency' => $request->currency,
                'customer' => $customer->id,
                'confirm'  => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'description' => 'Zakat',
            ]);


            // ✅ 8. Save local subscription (one-time marker)
            $localSubscription = $user->subscriptions()->create([
                'stripe_subscription_id' => 'one-time-' . $paymentIntent->id,
                'stripe_price_id'        => $price->id,
                'status'                 => 'ended',
                'price'                  => $request->zakat,
                'currency'               => strtolower($request->currency),
                'type'                   => $productName,
                'gift_aid'               => $request->gift_aid === 'yes' ? 'yes' : 'no',
                'start_date'             => now(),
                'end_date'               => now()->addSecond(),
                'canceled_at'            => now()->addSeconds(2),
            ]);

            // ✅ 9. Create local invoice
            $invoice = Invoice::create([
                'subscription_id'   => $localSubscription->id,
                'stripe_invoice_id' => $paymentIntent->id,
                'amount_due'        => $request->zakat,
                'currency'          => strtolower($request->currency),
                'invoice_date'      => now(),
                'paid_at'           => now(),
            ]);

            // ✅ 10. Create local transaction
            $transaction = Transaction::create([
                'invoice_id'            => $invoice->id,
                'stripe_transaction_id' => $paymentIntent->charges->data[0]->id ?? $paymentIntent->id,
                'paid_at'               => now(),
                'status'                => 'paid',
            ]);

            DB::commit();

            // ✅ 11. Notifications & Emails
            DB::afterCommit(function () use ($user, $localSubscription, $invoice) {
                $adminEmail = env('ADMIN_EMAIL');
                $admin = User::where('role', 'admin')->first();

                $currencySymbols = ['usd' => '$', 'gbp' => '£', 'eur' => '€'];
                $currencySymbol = $currencySymbols[strtolower($localSubscription->currency)] ?? strtoupper($localSubscription->currency);
                $userName = Str::title($user->name);
                $typeReadable = 'Zakat';

                // 🧍‍♂️ User + Admin Notifications
                $userTitle = "💝 {$typeReadable} Successful";
                $userMessage = "You donated {$currencySymbol}{$localSubscription->price} towards {$localSubscription->type}.";
                $adminTitle = "💰 New {$typeReadable} Received";
                $adminMessage = "{$userName} donated {$currencySymbol}{$localSubscription->price} towards {$localSubscription->type}.";

                $user->notify(new UserActionNotification($userTitle, $userMessage, 'user'));
                $admin?->notify(new UserActionNotification($adminTitle, $adminMessage, 'admin'));

                // 📨 Emails
                Mail::to($user->email)->send(new ZakatMail($user, $localSubscription));
                Mail::to($adminEmail)->send(new ZakatMail($user, $localSubscription, true));
            });

            return redirect()->back()->with('success', 'Zakat paid successfully! Payment received and invoice generated.');
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
