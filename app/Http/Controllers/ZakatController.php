<?php

namespace App\Http\Controllers;

use App\Mail\ZakatMail;
use App\Models\Invoice;
use App\Models\ProductCatalog;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\UserActionNotification;
use App\Rules\HasValidMx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Illuminate\Support\Str;

class ZakatController extends Controller
{
    public function index($currency, $zakat)
    {
        $currency = match ($currency) {
            '£' => 'gbp',
            '$' => 'usd',
            '€' => 'eur',
        };
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => intval($zakat * 100),
            'currency' => strtolower($currency),
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return view('zakah.form', [
            'currency' => $currency,
            'zakat' => $zakat,
            'clientSecret' => $paymentIntent->client_secret,
            // ider sy null beja ha currency ko 
            'userCurrency' => null,
        ]);
    }
    public function donateZakat(Request $request)
    {
        // 🔹 Normalize currency symbols and validate
        // dd($request->all());
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

            Stripe::setApiKey(env('STRIPE_SECRET'));
            if (!$user) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make('password'),
                    'role'     => 'donor',
                ]);
                $customer = Customer::create([
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'source' => $stripe_token,
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                if ($user->stripe_customer_id) {
                    $customer = Customer::retrieve([
                        'id' => $user->stripe_customer_id,
                        'expand' => ['sources'],
                    ]);
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
                $stripeProduct = Product::create(['name' => $productName]);
                $productCatalog = ProductCatalog::create([
                    'name' => $productName,
                    'product_id' => $stripeProduct->id,
                ]);
            } else {
                $stripeProduct = Product::retrieve(ProductCatalog::where('name', $productName)->first()->product_id);
            }
            // ✅ Create one-time Price
            $price = Price::create([
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

            return redirect()->back()->with('success', 'Zakat paid successfully! Please check your email for the donation details and receipt.');
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->getMessage());
        }
    }
    public function redirect(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntentId = $request->query('payment_intent');

        if (!$paymentIntentId) {
            return redirect()->route('root')->with('error', 'Payment could not be verified.');
        }

        // Fetch PaymentIntent from Stripe
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

        // Check status
        if ($paymentIntent->status === 'succeeded') {
            return redirect()->route('root')->with('success', 'Zakat paid successfully.');
        }

        return redirect()->route('root')->with('error', 'Payment was not completed.');
    }
}
