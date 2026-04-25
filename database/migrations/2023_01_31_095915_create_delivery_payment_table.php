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
        Schema::create('delivery_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('delivery_id')->index();
            $table->unsignedInteger('payment_id')->index();

            $table->foreign('payment_id')
                ->on('pay_methods')
                ->references('id')->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('delivery_id')
                ->on('deliveries')
                ->references('id')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_payment');
    }
};
