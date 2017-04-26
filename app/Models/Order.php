<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $hasDiscount = false;
    protected $discounts = [];
    protected $totalDiscount = 0;

    protected $fillable = [
        'customer_id',
        'total_in_cents'
    ];

    protected $appends = [
        'total',
        'display_discounts'
    ];

    protected $with = ['items'];


    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /*
    ==========================================================================
       Getters
    ==========================================================================
    */

    public function getDiscounts()
    {
        return $this->getDiscounts();
    }

    /*
    ==========================================================================
       Setters
    ==========================================================================
    */
    public function addDiscount($discount)
    {
        array_push($this->discounts, $discount);
    }

    public function sumDiscount($value)
    {
        $this->discount += $value;
    }

    public function setHasDiscount($value)
    {
        $this->hasDiscount = $value;
    }

    /*
    ==========================================================================
       Helpers
    ==========================================================================
    */

    public function canHaveDiscount()
    {
        return !$this->hasDiscount;
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

    public function getDisplayDiscountsAttribute()
    {
        return $this->discounts;
    }

}