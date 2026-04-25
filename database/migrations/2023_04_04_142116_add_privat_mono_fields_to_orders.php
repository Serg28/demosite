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
            $table->json('privat_payparts_info')->nullable();
            $table->json('mono_payparts_info')->nullable();
            $table->string('privat_order_id')->nullable();
            $table->string('mono_order_id')->nullable();
            $table->string('payparts_count')->nullable();
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
            $table->dropColumn('privat_payparts_info');
            $table->dropColumn('mono_payparts_info');
            $table->dropColumn('privat_order_id');
            $table->dropColumn('mono_order_id');
            $table->dropColumn('payparts_count');
        });
    }
};
