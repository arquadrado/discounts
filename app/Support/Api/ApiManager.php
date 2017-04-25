<?php

namespace App\Support\Api;

use App\Models\Discount;
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

	public function processOrder($order)
	{
		try {

			$processedOrder['body'] = $this->applyDiscounts($order);
			$processedOrder['status'] = 200;

		} catch (\Exception $e) {

			dd($e);
			$processedOrder['body'] = $order;

			$processedOrder['status'] = 500;

		}

		return $processedOrder;
	}

	public function applyDiscounts($order)
	{

		$discounts = Discount::where('active', 1)->orderBy('priority', 'desc')->get();

		return $discounts->reduce(function ($order, $discount) {

			if (array_key_exists('has_discount', $order) && $order['has_discount']) {

				return $order;

			}

			if (!array_key_exists('discount', $order)) {

				$order['discount'] = 0;

			}

			$discountValue = $discount->resolve($order);

			if ($discountValue) {

				if (!array_key_exists('discounts', $order)) {
					$order['discounts'] = [];
				}

				array_push($order['discounts'], $discount->description);

				$order['has_discount'] = $discount->cumulative === 1 ? false : true;

			}

			$order['discount'] += $discountValue;

			return $order;

		}, $order);

	}
}