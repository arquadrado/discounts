<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'product_id',
        'description',
        'price_in_dmcents',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
