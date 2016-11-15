<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlashSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->float('price');
            $table->date('starttime');
            $table->integer('special_id')->nullable();
            $table->string('code');
            $table->string('content');
            $table->string('averageprice')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('flash_sale_products');
    }
}
