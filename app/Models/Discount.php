<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model {

    protected $fillable = [
        'name',
        'value_in_percent',
        'type',
        'trigger_value',
        'threshold',
        'target',
        'product_id',
        'product_category_id',
        'active',
        'cumulative'
    ];

    protected $types = [
        'customer_revenue',
        'total_value',
        'product_category',
        'product'
    ];

    /*
    ==========================================================================
       Resolve the absolute value of the discount to apply to a given order
    ==========================================================================
    */

    public function resolve($order) {

        $resolverName = camel_case('resolve_'.$this->type);


        try {

            return $this->$resolverName($order);

        } catch (\Exception $e) {
            return 0;
        }

    }

    public function resolveCustomerRevenue($order)
    {
        $customer = Customer::find($order['customer-id']);

        if ($customer->revenue >= $this->trigger_value) {

            return floatval($order['total']) * $this->value_in_percent / 100;
        }

        return 0;
    }

    /*
    ==========================================================================
       Mutators
    ==========================================================================
    */

}
