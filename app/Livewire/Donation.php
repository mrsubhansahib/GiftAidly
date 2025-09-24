<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Stripe;
use Carbon\Carbon;

class Donation extends Component
{
    public $currency;
    public $amount;
    public $type;
    public $start_date;
    public $cancellation;
    public $fridays;
    public $payment_method_id;

    protected $rules = [
        'currency' => 'required|string',
        'amount' => 'required|numeric|min:1',
        'type' => 'required|string',
        'start_date' => 'required|date',
        'cancellation' => 'required|date|after_or_equal:start_date',
        'payment_method_id' => 'required|string',
    ];

    public function donate()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();

            // Ensure customer
            if (!$user->stripe_id) {
                $customer = Stripe\Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'payment_method' => $this->payment_method_id,
                    'invoice_settings' => [
                        'default_payment_method' => $this->payment_method_id,
                    ],
                ]);
                $user->update(['stripe_id' => $customer->id]);
            } else {
                $customer = Stripe\Customer::retrieve($user->stripe_id);
            }

            // Create product + price
            $product = Stripe\Product::create([
                'name' => ucfirst($this->type) . ' Donation',
            ]);

            $price = Stripe\Price::create([
                'unit_amount' => $this->amount * 100,
                'currency' => $this->currency,
                'recurring' => ['interval' => $this->type === 'Friday' ? 'week' : $this->type],
                'product' => $product->id,
            ]);

            // Date handling
            $startDate = Carbon::parse($this->start_date)->startOfDay();
            $endDate   = Carbon::parse($this->cancellation)->endOfDay();
            $startIsFuture = $startDate->isFuture();

            if (!$startIsFuture) {
                // Immediate start
                $subscription = Stripe\Subscription::create([
                    'customer' => $customer->id,
                    'items' => [['price' => $price->id]],
                    'cancel_at' => $endDate->timestamp,
                    'collection_method' => 'charge_automatically',
                    'default_payment_method' => $this->payment_method_id,
                ]);
            } else {
                // Scheduled
                $subscription = Stripe\Subscription::create([
                    'customer' => $customer->id,
                    'items' => [['price' => $price->id]],
                    'trial_end' => $startDate->timestamp,
                    'cancel_at' => $endDate->timestamp,
                    'collection_method' => 'charge_automatically',
                    'default_payment_method' => $this->payment_method_id,
                ]);
            }

            // Save locally
            $user->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $this->amount,
                'currency' => $this->currency,
                'type' => $this->type,
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date' => Carbon::createFromTimestamp($subscription->current_period_end),
                'canceled_at' => $endDate,
                'fridays' => $this->fridays, // only if Friday type
            ]);

            DB::commit();

            session()->flash('success', 'Donation setup successfully!');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.donation');
    }
}
