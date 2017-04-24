<?php

use Illuminate\Database\Seeder;


use App\Models\Discount;

class DiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $discounts = [
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
                'cumulative' => 0,
                'active' => 1,
            ],
            [
                'name' => 'Discount C',
                'description' => 'If client buys 2 or more items of the category 1 gets the 20% on the chepeast item',
                'value_in_percent' => 20,
                'type' => 'product_type',
                'trigger_value_in_cents' => 200,
                'threshold' => '=>',
                'target' => 'item',
                'product_category_id' => 1,
                'product_id' => null,
                'cumulative' => 0,
                'active' => 1,
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }

    }
}
