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

	public function getEndpoints()
	{
		return $this->routes;

	}

	public function processOrder($orderInfo)
	{
		try {

			$order = $this->getOrder($orderInfo);

			$processedOrder['body'] = $this->applyDiscounts($order);

			$processedOrder['status'] = 200;

		} catch (\Exception $e) {

			dd($e);
			$processedOrder['body'] = $orderInfo;

			$processedOrder['status'] = 500;

		}

		return $processedOrder;
	}

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

					$order->addDiscount($discount->description);

					$order->setHasDiscount($discount->cumulative === 1 ? false : true);

				}

				$order->discount += $discountValue;

				return $order;

			}, $order);

	}

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