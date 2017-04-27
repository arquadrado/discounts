<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'unit_price',
        'unit_price_in_cents',
        'total_in_cents'
    ];

    protected $appends = [
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

    /*
    ==========================================================================
       Mutators
    ==========================================================================
    */

    public function getTotalAttribute()
    {
        return $this->total_in_cents / 100;
    }

    public function getUnitValueAttribute()
    {
        return $this->unit_price_in_cents / 100;
    }
}
