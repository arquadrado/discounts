<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Order;

use App\Support\Facades\Discount;


class DiscountTest extends TestCase
{
	use DatabaseMigrations;

	private $categories = [
	    [
	        'id' => 1,
	        'name' => 'Switches'
	    ],
	    [
	        'id' => 2,
	        'name' => 'Tools'
	    ]
	];

	private $products = [
    	[
        	'id' => 'A101',
		    'description' => "Screwdriver",
		    'category' => "1",
		    'price' => "9.75"
        ],
        [
		    'id' => 'A102',
		    'description' => 'Electric screwdriver',
		    'category' => '1',
		    'price' => '49.50'
		],
		[
		    'id' => 'B101',
		    'description' => 'Basic on-off switch',
		    'category' => '2',
		    'price' => '4.99'
		],
		[
		    'id' => 'B102',
		    'description' => 'Press button',
		    'category' => '2',
		    'price' => '4.99'
		],
		[
		    'id' => 'B103',
		    'description' => 'Switch with motion detector',
		    'category' => '2',
		    'price' => '12.95'
		]
    ];

    private $customers = [
    	[
		    'id' => '1',
		    'name' => 'Coca Cola',
		    'since' => '2014-06-28',
		    'revenue' => '492.12'
		],
		[
		    'id' => '2',
		    'name' => 'Teamleader',
		    'since' => '2015-01-15',
		    'revenue' => '1505.95'
		],
		[
		    'id' => '3',
		    'name' => 'Jeroen De Wit',
		    'since' => '2016-02-11',
		    'revenue' => '0.00'
		]
    ];

    private $discounts = [
        [
            'name' => 'Discount A',
            'description' => 'If client has spent more than 1000 euros gets a 10% discount on the order\'s total value',
            'value_in_percent' => 10,
            'type' => 'customer_revenue',
            'trigger_value_in_cents' => 100000,
            'threshold' => '>',
            'target' => 'total',
            'product_category_id' => null,
            'product_id' => null,
            'priority' => 3,
            'repeat' => 0,
            'cumulative' => 0,
            'active' => 1,
        ],
        [
            'name' => 'Discount B',
            'description' => 'For every 5 items of the category 2 the sixth free',
            'value_in_percent' => 100,
            'type' => 'product_type',
            'trigger_value_in_cents' => 500,
            'threshold' => '>',
            'target' => 'item',
            'product_category_id' => 2,
            'product_id' => null,
            'priority' => 2,
            'repeat' => 1,
            'cumulative' => 0,
            'active' => 1,
        ],
        [
            'name' => 'Discount C',
            'description' => 'If client buys 2 or more items of the category 1 gets the 20% on the chepeast item',
            'value_in_percent' => 20,
            'type' => 'product_type',
            'trigger_value_in_cents' => 200,
            'threshold' => '>=',
            'target' => 'item|min',
            'product_category_id' => 1,
            'product_id' => null,
            'priority' => 1,
            'repeat' => 0,
            'cumulative' => 0,
            'active' => 1,
        ],
    ];

    private $orders = [
    	[
    		'id' => 1,
			'customer-id' => 1,
			'items' => [
			    [
			      'product-id' => 'B102',
			      'quantity' => 10,
			      'unit-price' => '4.99',
			      'total' => '49.90'
			    ]
			],
			'total' => '49.90'
    	],
    	[
    		'id' => 2,
			'customer-id' => 2,
			'items' => [
			    [
			      'product-id' => 'B102',
			      'quantity' => 5,
			      'unit-price' => '4.99',
			      'total' => '24.95'
			    ]
			],
			'total' => '24.95'
    	],
    	[
    		'id' => 3,
			'customer-id' => 3,
			'items' => [
			    [
			      'product-id' => 'A101',
			      'quantity' => 2,
			      'unit-price' => '9.75',
			      'total' => '19.50'
			    ],
			    [
			      'product-id' => 'A102',
			      'quantity' => 1,
			      'unit-price' => '49.50',
			      'total' => '49.50'
			    ]
			],
			'total' => '69.00'
    	]
    ];


    /**
     * A basic test example.
     *
     * @return void
     */

    public function seedDatabase()
    {
    	if (!is_null($this->categories)) {

	        foreach ($this->categories as $category) {

	            factory(\App\Models\ProductCategory::class)->create($category);

	        }
    	}

    	if (!is_null($this->products)) {

	        foreach ($this->products as $product) {

	        	factory(\App\Models\Product::class)->create([
	                'product_id' => $product['id'],
	                'description' => $product['description'],
	                'category_id' => $product['category'],
	                'price_in_dmcents' => (int)floor(floatval($product['price']) * 100),
	            ]);

	        }
    	}


        if (!is_null($this->customers)) {

	        foreach ($this->customers as $customer) {

	        	factory(\App\Models\Customer::class)->create([
	                'name' => $customer['name'],
	                'since' => $customer['since'],
	                'revenue_in_dmcents' => (int)floor(floatval($customer['revenue']) * 100),
	            ]);
	        }
        }

        if (!is_null($this->discounts)) {

	        foreach ($this->discounts as $discount) {
	        	factory(\App\Models\Discount::class)->create($discount);
	        }
        }

        if (!is_null($this->orders)) {

        	foreach ($this->orders as $order) {
        		$createdOrder = factory(\App\Models\Order::class)->create([
        			'customer_id' => $order['customer-id'],
        			'total_in_cents' => (int)floor(floatval($order['total']) * 100)
        		]);

        		foreach ($order['items'] as $item) {
					factory(\App\Models\OrderItem::class)->create([
	        			'product_id' => $item['product-id'],
	        			'order_id' => $createdOrder->id,
	        			'quantity' => $item['quantity'],
	        			'unit_price' => $item['unit-price'],
	        			'unit_price_in_cents' => (int)floor(floatval($item['unit-price']) * 100),
	        			'total_in_cents' => (int)floor(floatval($item['total']) * 100)
	        		]);
        		}
        	}
        }


    }


	public function testDiscounts()
	{

		$this->seedDatabase();

		foreach ($this->discounts as $discount) {
			$this->assertDatabaseHas('discounts', $discount);
		}

		$orderA = Order::find(1);
		$orderB = Order::find(2);
		$orderC = Order::find(3);


        $this->assertEquals(Discount::applyDiscounts($orderA)['discount'], 4.99);

        $this->assertEquals(Discount::applyDiscounts($orderB)['discount'], 2.495);

        $this->assertEquals(Discount::applyDiscounts($orderC)['discount'], 1.95);

	}
}
