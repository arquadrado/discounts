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

            $table->string('product_id')->nullable();

            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('quantity')->unsigned();

            $table->string('unit_price')->nullable();
            $table->integer('unit_price_in_cents')->unsigned()->nullable();
            $table->integer('total_in_cents')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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
            $table->dropForeign('product_id_order_items_foreign');
            $table->dropForeign('order_id_order_items_foreign');
        });
    }
}
