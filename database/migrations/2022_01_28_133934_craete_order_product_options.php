<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CraeteOrderProductOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_products_id')->index();
            $table->unsignedInteger('characteristic_id')->index();
            $table->unsignedInteger('characteristic_option_id')->index();

            $table->foreign('order_products_id')
                ->references('id')
                ->on('order_products')->onDelete('cascade');

            $table->foreign('characteristic_id')
                ->references('id')
                ->on('characteristics')->onDelete('cascade');

            $table->foreign('characteristic_option_id')
                ->references('id')
                ->on('characteristic_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_options');
    }
}
