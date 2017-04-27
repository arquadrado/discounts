<?php

namespace App\Support\Api;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Routing\RouteCollection;

class ApiManager
{

    /*
    |--------------------------------------------------------------------------
    | Api Manager
    |--------------------------------------------------------------------------
    |
    | Responsible for all api management. Avoids placing api logic directly into the controller.
    |
    */

    public function __construct()
    {

	    $this->routes = collect(app()->routes->getRoutes())->filter(function ($route) {

		    return $route->action['prefix'] === 'api' && $route->uri !== 'api';

	    })->reduce(function ($reduced, $route) {

		    $reduced[$route->action['as']] = [
				'uri' => $route->uri,
				'name' => $route->action['as'],
				'methods' => $route->methods
			];

			return $reduced;

		}, []);

	}

	/*
    ==========================================================================
       Lists all api endpoints
    ==========================================================================
    */

	public function getEndpoints()
	{
		return $this->routes;

	}

	/*
    ==========================================================================
       Process incoming order
    ==========================================================================
    */

	public function processOrder($orderInfo)
	{
		try {

			$order = $this->getOrder($orderInfo);

			$processedOrder['body'] = $this->applyDiscounts($order);

			$processedOrder['status'] = 200;

		} catch (\Exception $e) {

			$processedOrder['body'] = ['error' => $e->getMessage()];

			$processedOrder['status'] = 500;

		}

		return $processedOrder;
	}

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

	/*
    ==========================================================================
       Gets and previously stored order or transform the provided json order to eloquent model and returns
    ==========================================================================
    */

	public function getOrder($orderInfo)
	{

		if (isset($orderInfo['id'])){

			$order = Order::find($orderInfo['id']);
		}

		if (!isset($orderInfo['id']) || is_null($order)) {
			$order = Order::create([
				'customer_id' => $orderInfo['customer_id'],
    			'total_in_cents' => (int)floor(floatval($orderInfo['total']) * 100)
			]);

			foreach ($orderInfo['items'] as $item) {
				OrderItem::create([
        			'product_id' => $item['product_id'],
        			'order_id' => $order->id,
        			'quantity' => $item['quantity'],
        			'unit_price' => $item['unit_price'],
        			'unit_price_in_cents' => (int)floor(floatval($item['unit_price']) * 100),
        			'total_in_cents' => (int)floor(floatval($item['total']) * 100)
        		]);
			}
		}


		return $order;
	}
}
