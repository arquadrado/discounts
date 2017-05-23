<?php

namespace App\Services;

use App\Contracts\DiscountServiceContract;

use App\Models\Discount;
use App\Models\Order;

class AnotherDiscountService implements DiscountServiceContract
{
    /*
    ==========================================================================
       Resolve discounts to be applied
    ==========================================================================
    */

    public function applyDiscounts(Order $order)
    {
        throw new \Exception('I am also a discount service that could have been plugged in only by setting the config file discount.service');
    }
}
