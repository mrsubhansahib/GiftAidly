<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'stripe_transaction_id' => 'txn_' . $this->faker->unique()->regexify('[A-Za-z0-9]{14}'),
            'paid_at' => now(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            
        ];
    }
}
