<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'GiftAidly Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'role' => 'admin',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        DB::table('subscriptions')->insert(
            [
                'user_id' => 1,
                'stripe_subscription_id' => 'sub_1N2mXyL6jYp0Xx',
                'stripe_price_id' => 'price_1N2mWXL6jYp0Xx',
                'status' => 'active',
                'price' => 5000,
                'currency' => 'usd',
                'type' => 'monthly',
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'canceled_at' => now(),
            ]
        );

        DB::table('invoices')->insert(
            [
                'subscription_id' => 1,
                'stripe_invoice_id' => 'in_1N2mY2L6jYp0Xx',
                'amount_due' => 5000,
                'currency' => 'usd',
                'invoice_date' => now(),
                'paid_at' => now(),
            ]
        );
        DB::table('transactions')->insert(
            [
                'invoice_id' => 1,
                'stripe_transaction_id' => 'txn_1N2mZAL6jYp0Xx',
                'paid_at' => now(),
                'status' => 'succeeded',
            ]
        );
    }
}
