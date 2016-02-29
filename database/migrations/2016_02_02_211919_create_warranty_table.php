<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarrantyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warrantys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_name');
            $table->string('model_code');
            $table->string('staff');
            $table->string('client_name');
            $table->string('client_phone');
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
        Schema::drop('warrantys');
    }
}
