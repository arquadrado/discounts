<?php

use Illuminate\Database\Seeder;


use App\Models\Order;
use App\Models\OrderItem;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = [
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

        if (!is_null($orders)) {

            foreach ($orders as $order) {
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
}
