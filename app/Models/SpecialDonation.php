<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialDonation extends Model
{
    protected $fillable = ['name', 'price', 'currency'];
    protected $table = 'special_donations';
}
