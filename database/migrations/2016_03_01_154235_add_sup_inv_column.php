<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupInvColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warrantys', function (Blueprint $table) {
            $table->string("purchase_inv")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warrantys', function (Blueprint $table) {
            $table->dropColumn("purchase_inv");
        });
    }
}
