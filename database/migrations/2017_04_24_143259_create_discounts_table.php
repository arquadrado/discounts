<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('value_in_percent')->unsigned();
            $table->string('type');
            $table->integer('trigger_value')->unsigned();
            $table->string('threshold');
            $table->string('target');
            $table->integer('product_category_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->boolean('cumulative')->default(0);
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('discounts', function(Blueprint $table) {
        });
    }
}
