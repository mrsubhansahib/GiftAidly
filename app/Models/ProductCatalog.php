<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCatalog extends Model
{
    protected $fillable = ['name', 'product_id'];
    protected $table = 'product_catalog';
}
