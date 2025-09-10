<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_subscription_id' => 'sub_' . $this->faker->unique()->regexify('[A-Za-z0-9]{14}'),
            'stripe_price_id' => 'price_' . $this->faker->unique()->regexify('[A-Za-z0-9]{14}'),
            'status' => $this->faker->randomElement(['active', 'canceled']),
            'price' => $this->faker->numberBetween(10, 100),
            'currency' => 'usd',
            'type' => $this->faker->randomElement(['monthly', 'yearly']),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'canceled_at' => now()->addMonth(),
        ];
    }
}
