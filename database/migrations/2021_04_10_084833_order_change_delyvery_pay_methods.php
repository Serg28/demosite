<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderChangeDelyveryPayMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery', 'pay_method']);

            $table->unsignedInteger('delivery_id')->nullable()->index();
            $table->unsignedInteger('pay_method_id')->nullable()->index();

            $table->foreign('delivery_id')
                ->references('id')
                ->on('deliveries')->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('pay_method_id')
                ->references('id')
                ->on('pay_methods')->onUpdate('cascade')->onDelete('restrict');
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
            $table->string('delivery');
            $table->string('pay_method');

            $table->dropColumn(['delivery_id', 'pay_method_id']);
        });
    }
}
