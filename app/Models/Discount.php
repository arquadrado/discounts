<?php

namespace App\Models;

use App\Models\Traits\AssertEqualities;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{

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

    public function resolve($order)
    {

        $resolverName = camel_case('resolve_'.$this->type);

        try {

            return $this->$resolverName($order);

        } catch (\Exception $e) {

            throw new \Exception("Error Processing Request", 1);
        }

    }

    public function resolveCustomerRevenue($order)
    {
        /*
        ==========================================================================
           Finds the customer to which the order belongs
        ==========================================================================
        */
        $customer = Customer::find($order->customer_id);

        
        /*
        ==========================================================================
           This discount will return zero if the no customer is found
        ==========================================================================
        */
        if (is_null($customer)) {
            return 0;
        }
        
        
        /*
        ==========================================================================
           Otherwise it will be checked if the value of the customer revenue should trigger the discount and apply the discount if so
        ==========================================================================
        */

        if ($this->shouldTrigger($customer->revenue, $this->trigger_value, $this->threshold)) {

            return floatval($order->total) * $this->value;
        }

        return 0;
    }

    public function resolveProductType($order)
    {
        
        /*
        ==========================================================================
           Gets all the products in the database
        ==========================================================================
        */
        $products = Product::get();
        
        
        /*
        ==========================================================================
           Filter the items in the orders to get only the ones that are from the desired category
        ==========================================================================
        */
        $items = $order->items->filter(function ($item) use ($products) {


            $product = $products->where('product_id', $item->product_id)->first();


            if (is_null($product)) {

                return false;
            }

            return $product->category->id == $this->product_category_id;
        });

        /*
        ==========================================================================
           Gets the total quantity of products of the desired category
        ==========================================================================
        */
        $totalQuantity = $items->reduce(function ($total, $item) {

            $total += (int)$item->quantity;

            return $total;

        }, 0);

         /*
        ==========================================================================
           At this point there is no information about what product of the category should the discount apply so the first one is selected
        ==========================================================================
        */
        $item = $items->first();

         /*
        ==========================================================================
           If there are no items the discount will evaluate to zero and return
        ==========================================================================
        */
        if (is_null($item)) {
            return 0;
        }
        
        /*
        ==========================================================================
           Here we will be checking if there is a more specific target among the set of products
           The options can be defined by setting them in the discount target column
           At this point only 'min' is implemented
           This option will target the product of the set that has the lowest value
        ==========================================================================
        */
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
        
        /*
        ==========================================================================
           Here we'll start be set the number of items affected by the discount to one as default
           If the discount as the attribute 'repeat' set to true, this means that this discount can be applied
           as long as there are items in enough quantity to meet the trigger value multiple times
        ==========================================================================
        */

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
        /*
        ==========================================================================
           Checks if the order value reaches the trigger value and return the discount value accordingly
        ==========================================================================
        */
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
