<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeCheckout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('checkout_id')->index()->nullable();

            $table->foreign('checkout_id')->on('checkouts')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn(['checkout_id']);
        });
    }
}
