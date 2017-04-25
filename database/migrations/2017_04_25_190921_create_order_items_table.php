<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {

            $table->increments('id');

            $table->string('product-id');

            $table->integer('order-id')->unsigned();
            $table->integer('quantity')->unsigned();

            $table->string('unit-price');
            $table->integer('unit_price_in_cents')->unsigned();
            $table->integer('total_in_cents')->unsigned();

            $table->timestamps();

            /*$table->foreign('product-id')->references('product_id')->on('products')->onDelete('cascade');

            $table->foreign('order-id')->references('id')->on('orders')->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_items', function(Blueprint $table) {
            //$table->dropForeign('productid_order_items_foreign');
            //$table->dropForeign('order-id_order_items_foreign');
        });
    }
}
