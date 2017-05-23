<?php

namespace App\Services;

use App\Contracts\DiscountServiceContract;

use App\Models\Discount;
use App\Models\Order;

class DiscountService implements DiscountServiceContract
{
    /*
    ==========================================================================
       Resolve discounts to be applied
    ==========================================================================
    */

    public function applyDiscounts(Order $order)
    {
        return Discount::where('active', 1)
            ->orderBy('priority', 'desc')
            ->get()
            ->reduce(function ($order, $discount) {

                if (!$order->canHaveDiscount()) {
                    return $order;
                }


                $discountValue = $discount->resolve($order);

                if ($discountValue) {
                    $order->addDiscount([
                        'value' => $discountValue,
                        'description' => $discount->description]);

                    $order->setCanHaveDiscount($discount->cumulative === 1 ? false : true);
                }

                return $order;
            }, $order);
    }
}
