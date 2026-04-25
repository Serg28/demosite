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
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->tinyInteger('is_active_prepayment')->default(0)->comment('Активировать предоплату. 1 - Да');
            $table->integer('prepayment_percent')->default(0)->comment('Процент предоплаты, если предусмотрена');
            $table->integer('min_prepayment_amount')->default(0)->comment('Минимальная сумма предоплаты');
            $table->integer('min_order_amount')->default(0)->comment('Сумма заказа, от которой доступно');
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
            $table->dropColumn(['prepayment_percent','min_order_amount','min_prepayment_amount','is_active_prepayment']);
        });
    }
};
