<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantitySupInvColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warrantys', function (Blueprint $table) {
            $table->integer("quantity")->default(1);
            $table->string("purchase_inv")->nullable();
            $table->string("purchase_date")->nullable();
            $table->string("sale_inv")->nullable();
            $table->string("sale_date")->nullable();
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
            $table->dropColumn("quantity");
            $table->dropColumn("purchase_inv");
            $table->dropColumn("purchase_date");
            $table->dropColumn("sale_inv");
            $table->dropColumn("sale_date");
        });
    }
}
