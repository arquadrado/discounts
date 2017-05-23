<?php

namespace App\Contracts;

use App\Models\Order;

interface DiscountServiceContract
{
    public function applyDiscounts(Order $order);
}
