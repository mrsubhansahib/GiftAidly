<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'stripe_transaction_id',
        'paid_at',
        'status',
    ];
    protected $table = 'transactions';
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
