<?php

namespace App\Livewire;

use App\Models\SpecialDonation as ModelsSpecialDonation;
use Livewire\Component;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Models\User;


class SpecialDonation extends Component
{
    public $name, $email, $currency = 'gbp', $amount, $special;
    public $clientSecret = null;

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|string|email:rfc|email:dns',
        'special' => 'required|exists:special_donations,id',
        'currency' => 'required|in:gbp,usd,eur',
        'amount' => 'required|numeric|min:1',
    ];

    public function updated($field)
    {
        if ($this->validateOnly($field)) {
            if ($this->name && $this->email && $this->special && $this->amount && !$this->clientSecret) {
                $this->createIntent();
            }
        }
    }

    public function createIntent()
    {
        $this->validate();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 🧩 1. Create or find user
        $user = User::firstOrCreate(
            ['email' => $this->email],
            ['name' => $this->name, 'role' => 'donor']
        );

        // 🧩 2. Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create(['email' => $user->email, 'name' => $user->name]);
            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        // 🧩 3. Create PaymentIntent (one-time)
        $intent = PaymentIntent::create([
            'amount' => intval($this->amount * 100),
            'currency' => $this->currency,
            'customer' => $customer->id,
            'automatic_payment_methods' => ['enabled' => true],
            'description' => 'Special Donation',
        ]);

        $this->clientSecret = $intent->client_secret;

        // dispatch event to JS
        $this->dispatch('special-stripe-init', clientSecretSpecial: $this->clientSecret);
    }

    public function render()
    {
        $specials = ModelsSpecialDonation::all();
        return view('livewire.special-donation', compact('specials'));
    }
}
