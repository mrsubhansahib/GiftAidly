<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCatalog extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'product_id'];
    protected $table = 'product_catalog';
}
