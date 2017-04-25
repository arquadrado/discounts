<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {

    protected $fillable = [
        'product-id',
        'quantity',
        'unit-price',
        'total'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute()
    {
        return $this->total_in_cents / 100;
    }


}