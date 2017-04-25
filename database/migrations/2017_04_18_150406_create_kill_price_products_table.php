<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKillPriceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kill_price_products', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('model')->unique();
            $table->float('product_id');
            $table->string('url');
            $table->string('status')->default('y');
            $table->float('bottomPrice');
            $table->text('target')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('kill_price_products');
    }
}
