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
            dd($e);
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

    public function resolveProductType($order)
    {
        $categoryId = $this->product_category_id;

        $items = collect($order['items'])->filter(function ($item) {

            $product = Product::where('product_id', $item['product-id'])->first();

            if (is_null($product)) {

                return false;
            }

            return $product->category->id === $this->product_category_id;
        });

        $totalQuantity = $items->reduce(function ($total, $item) {

            $total += $item['quantity'];

            return $total;

        }, 0);
        

        $item = $items->first();

        if (!is_null($this->target)) {

            $exploded = explode('|', $this->target);

            $targetValue = array_pop($exploded);

            if ($targetValue === 'min') {

                $item = $items->reduce(function ($reduced, $item) {

                    if (is_null($reduced)) {
                        $reduced = $item;
                    }

                    $reduced = $reduced['unit-price'] < $item['unit-price'] ? $reduced : $item;

                    return $reduced;

                }, null);
            }

        }

        $affectedItems = 1;

        if ($this->repeat) {

            $affectedItems = floor($totalQuantity / ($this->trigger_value + 1));

        }

        if ($totalQuantity > $this->trigger_value) {

            return $item['unit-price'] * $affectedItems * $this->value_in_percent / 100;
        }

        return 0;

    }

    public function resolveTotalValue($order)
    {
        if (floatval($order['total']) >= $this->trigger_value) {

            return floatval($order['total']) * $discount->value_in_percent / 100;

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
        if ($this->type === 'product_type' || $this->type === 'product_type') {

            return round($this->trigger_value_in_cents / 100);

        }

        return $this->trigger_value_in_cents / 100;
    }

}
