<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class Zakat extends Component
{
    public $name, $email, $currency, $amount;
    public $clientSecret = null;

    protected $rules = [
        'name'  => 'required|string|max:255',
        'email'    => 'email|required|string|email:rfc|email:dns',
    ];

    public function mount($currency, $amount)
    {
        $this->currency = $currency;
        $this->amount   = $amount;
    }

    public function updated($field)
    {
        if ($this->validateOnly($field)) {
            if ($this->name && $this->email && !$this->clientSecret) {
                $this->createIntent();
            }
        }
    }

    public function createIntent()
    {
        $this->validate();
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'role'     => 'donor',
            ]
        );

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $this->email,
                'name'  => $this->name,
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        $intent = PaymentIntent::create([
            'amount' => intval($this->amount * 100),
            'currency' => $this->currency,
            'customer' => $customer->id,
            'description' => 'Zakat',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        $this->clientSecret = $intent->client_secret;

        // IMPORTANT FIX: event name must match parent JS
        $this->dispatch('stripe-init', clientSecret: $this->clientSecret);
    }

    public function render()
    {
        return view('livewire.zakat');
    }
}
