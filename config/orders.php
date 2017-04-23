<?php
	
	//this file mocks the DB structure

	$customers = file_get_contents('../resources/assets/mocks/data/customers.json');
	$products = file_get_contents('../resources/assets/mocks/data/products.json');

	return [

		'discounts' => [

			'identifiers' => [

				[
					'name' => 'Discount A',
					'value' => 10,
					'trigger' => [
						'type' => 'customer_revenue',
						'value' => 1000,
						'threshold' => '=',
						'target' => null
					],
					'cumulative' => false
				],

				/*[
					'name' => 'Discount D',
					'value' => 20,
					'trigger' => [
						'type' => 'customer_revenue',
						'value' => 400,
						'threshold' => '=',
						'target' => null
					],
					'cumulative' => false
				],*/

				[
					'name' => 'Discount B',
					'value' => 100,
					'trigger' => [
						'type' => 'product_type',
						'value' => 5,
						'threshold' => '=',
						'target' => null,
						'product_category' => 2,
						'repeat' => true
					
					],
					'cumulative' => false
				],

				[
					'name' => 'Discount C',
					'value' => 20,
					'trigger' => [
						'type' => 'product_type', 	
						'value' => 2,
						'threshold' => '>',
						'target' => 'item|min',
						'product_category' => 1,
						'repeat' => false
					],
					'cumulative' => false
				],

				[
					'name' => 'Discount E',
					'value' => 50,
					'trigger' => [
						'type' => 'total_value', 	
						'value' => 100,
						'threshold' => '>',
						'target' => null
					],
					'cumulative' => false
				],

			],

			'triggers' => [

				'customer_revenue' => [
					'target' => 'total'
				],
				'product' => [
					'target' => 'item'
				],
				'product_type' => [
					'target' => 'item'
				],
				'total_value' => [
					'target' => 'total'
				]

			],

		],

		'products' => json_decode($products, true),

		'customers' => json_decode($customers, true)
	];