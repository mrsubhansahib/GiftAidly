<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'price',
        'currency',
        'type',
        'start_date',
        'end_date',
        'canceled_at',
    ];
    protected $table = 'subscriptions';
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
