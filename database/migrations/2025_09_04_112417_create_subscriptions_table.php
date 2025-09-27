<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('stripe_subscription_id')->unique();
            $table->string('stripe_price_id');
            $table->enum('gift_aid', ['yes', 'no'])->default('no');
            $table->string('status');
            $table->string('price');
            $table->string('currency');
            $table->string('type');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamp('canceled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
