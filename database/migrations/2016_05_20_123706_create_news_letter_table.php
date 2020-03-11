<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsLetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_letter', function (Blueprint $table) {
		$table->increments('id');
            	$table->timestamps();
            	$table->string('firstname')->nullable();
	    	$table->string('lastname')->nullable();
		$table->string('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_letter', function (Blueprint $table) {
            Schema::drop('news_letter');
        });
    }
}
