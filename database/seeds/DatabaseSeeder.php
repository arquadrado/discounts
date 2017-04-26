<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MockDataSeeder::class);
        $this->call(DiscountsSeeder::class);
        $this->call(OrdersSeeder::class);

    }
}
