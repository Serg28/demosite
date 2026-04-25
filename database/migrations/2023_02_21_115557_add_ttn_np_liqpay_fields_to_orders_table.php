<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_num')->nullable();
            $table->json('np_info')->nullable();
            $table->json('liqpay_info')->nullable();
            $table->string('liqpay_order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_num']);
            $table->dropColumn(['np_info']);
            $table->dropColumn(['liqpay_info']);
            $table->dropColumn(['liqpay_order_id']);
        });
    }
};
