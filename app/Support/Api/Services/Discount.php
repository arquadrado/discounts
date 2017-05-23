<?php

namespace App\Support\Services;

use App\Contracts\DiscountServiceContract;
use App\Models\Order;

class Discount
{
    protected $service;

    public function __construct(DiscountServiceContract $service)
    {
        $this->service = $service;
    }

    public function applyDiscounts(Order $order)
    {
        return $this->service->applyDiscounts($order);
    }
}
