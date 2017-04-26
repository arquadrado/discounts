<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = [
        'customer-id',
        'total'
    ];

    protected $appends = [
        'total'
    ];

    protected $with = ['items'];


    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order-id');
    }

    public function getTotalAttribute()
    {
    	return $this->total_in_cents / 100;
    }


}