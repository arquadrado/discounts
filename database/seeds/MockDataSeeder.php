<?php

use Illuminate\Database\Seeder;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = [
            [
                'id' => 1,
                'name' => 'Switches'
            ],
            [
                'id' => 2,
                'name' => 'Tools'
            ]
        ];

        foreach ($categories as $category) {

            ProductCategory::create($category);

        }

        $products = json_decode(file_get_contents(storage_path('mock_data/products.json')));

        foreach ($products as $product) {

            Product::create([
                'product_id' => $product->id,
                'description' => $product->description,
                'category_id' => $product->category,
                'price_in_dmcents' => (int)floor(floatval($product->price) * 100),
            ]);
        }

        $customers = json_decode(file_get_contents(storage_path('mock_data/customers.json')));

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer->name,
                'since' => $customer->since,
                'revenue_in_dmcents' => (int)floor(floatval($customer->revenue) * 100),
            ]);
        }

    }
}
