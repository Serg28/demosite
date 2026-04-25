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
        Schema::create('warehouse_order_product', function (Blueprint $table) {
            $table->id();
            //$table->json('warehouse_name');
            $table->unsignedBigInteger('warehouse_id')->unsigned()->nullable()->index();
            $table->unsignedBigInteger('order_id')->unsigned()->nullable()->index();
            $table->unsignedBigInteger('product_id')->unsigned()->nullable()->index();
            $table->integer('count');
            $table->string('status');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('warehouse_id')
                ->references('id')->on('warehouses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('order_id')
                ->references('id')->on('orders')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('product_id')
                ->references('id')->on('products')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_order_product');
    }
};
