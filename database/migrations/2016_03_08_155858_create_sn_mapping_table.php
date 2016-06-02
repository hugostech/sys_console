<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sn_mapping', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('warranty_id');
            $table->string('original_sn');
            $table->string('sn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sn_mapping');
    }
}
