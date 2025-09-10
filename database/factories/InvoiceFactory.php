<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'stripe_invoice_id' => 'in_' . $this->faker->unique()->regexify('[A-Za-z0-9]{14}'),
            'amount_due' => $this->faker->numberBetween(10, 100),
            'currency' => 'usd',
            'invoice_date' => now(),
            'paid_at' => now(),
        ];
    }
}
