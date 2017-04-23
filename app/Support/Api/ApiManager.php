<?php 

namespace App\Support\Api;

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

		$discounts = collect(config('orders.discounts.identifiers'));

		return $discounts->reduce(function ($order, $discount) {

			if (array_key_exists('has_discount', $order) && $order['has_discount']) {

				return $order;

			}


			if (!array_key_exists('discount', $order)) {

				$order['discount'] = 0;

			}

			$discountValue = $this->resolveDiscountType($discount, $order);

			if ($discountValue) {

				$order['has_discount'] = !$discount['cumulative'];

			}


			$order['discount'] += $discountValue;

			return $order; 

		}, $order);

	}

	public function resolveDiscountType($discount, $order)
	{

		switch ($discount['trigger']['type']) {

			case 'customer_revenue': 

				$customer = collect(config('orders.customers'))->filter(function ($customer) use ($order) {

									return $customer['id'] == $order['customer-id'];

								})->first();



				if (floatval($customer['revenue']) >= floatval($discount['trigger']['value'])) {

					return floatval($order['total']) * $discount['value'] / 100;  

				}

				return 0;

			case 'product':

				return 0;

			case 'product_type':

				$categoryId = $discount['trigger']['product_category'];



				$item = collect($order['items'])->filter(function ($item) use ($categoryId) {

									return $this->belongsToCategory($categoryId, $item);
								})->first();


				if ($discount['trigger']['repeat']) {


					$affectedItems = floor($item['quantity'] / ($discount['trigger']['value'] + 1));  


					if ($item['quantity'] > $discount['trigger']['value']) {
						return $item['unit-price'] * $affectedItems * $discount['value'] / 100; 
					}

					return 0;
				}

				if ($item['quantity'] > $discount['trigger']['value']) {
					return $item['unit-price'] * $discount['value'] / 100; 
				}

				return 0;

			case 'total_value':

				if (floatval($order['total']) >= floatval($discount['trigger']['value'])) {

					return floatval($order['total']) * $discount['value'] / 100;  

				}

				return 0;

			default: 

				return 0;
		}

	}

	public function belongsToCategory($categoryId, $item)
	{
		$products = config('orders.products');

		foreach ($products as $product) {


			if ($product['id'] == $item['product-id']) {

				return $product['category'] == $categoryId;

			}
		}

		return false;
	}
}