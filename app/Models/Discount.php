<?php

namespace App\Models;

use App\Models\Traits\AssertEqualities;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model {

    use AssertEqualities;

    const TYPE_CUSTOMER_REVENUE = 'customer_revenue';
    const TYPE_TOTAL_VALUE = 'total_value';
    const TYPE_PRODUCT_CATEGORY = 'product_category';
    const TYPE_PRODUCT = 'product';

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
            dd($e);
            return 0;
        }

    }

    public function resolveCustomerRevenue($order)
    {
        $customer = Customer::find($order->customer_id);


        if (is_null($customer)) {
            return 0;
        }

        if ($this->shouldTrigger($customer->revenue, $this->trigger_value, $this->threshold)) {

            return floatval($order->total) * $this->value;
        }

        return 0;
    }

    public function resolveProductType($order)
    {
        $products = Product::get();

        $items = $order->items->filter(function ($item) use ($products) {


            $product = $products->where('product_id', $item->product_id)->first();


            if (is_null($product)) {

                return false;
            }

            return $product->category->id == $this->product_category_id;
        });


        $totalQuantity = $items->reduce(function ($total, $item) {

            $total += (int)$item->quantity;

            return $total;

        }, 0);


        $item = $items->first();

        if (is_null($item)) {
            return 0;
        }

        if (!is_null($this->target)) {

            $exploded = explode('|', $this->target);

            $targetValue = array_pop($exploded);

            if ($targetValue === 'min') {

                $item = $items->reduce(function ($reduced, $item) {

                    if (is_null($reduced)) {
                        $reduced = $item;
                    }

                    $reduced = $reduced->unit_price < $item->unit_price ? $reduced : $item;

                    return $reduced;

                }, null);
            }

        }

        $affectedItems = 1;

        if ($this->repeat) {

            $affectedItems = floor($totalQuantity / ($this->trigger_value + 1));

        }

        if ($this->shouldTrigger($totalQuantity, $this->trigger_value, $this->threshold)) {

            return $item->unit_value * $affectedItems * $this->value;

        }

        return 0;

    }

    public function resolveTotalValue($order)
    {

        if ($this->shouldTrigger($order->total, $this->trigger_value, $this->threshold)) {

            return $order->total * $this->value;

        }

        return 0;
    }

    /*
    ==========================================================================
       Mutators
    ==========================================================================
    */

    public function getTriggerValueAttribute()
    {
        if ($this->type === self::TYPE_PRODUCT_CATEGORY || $this->type === self::TYPE_PRODUCT) {

            return round($this->trigger_value_in_cents / 100);

        }

        return $this->trigger_value_in_cents / 100;
    }

    public function getValueAttribute()
    {
        if (is_null($this->value_in_percent)) {

            return 0;

        }

        return $this->value_in_percent / 100;
    }

}
