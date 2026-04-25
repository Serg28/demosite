<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUnfinishedBasketsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unfinished_baskets_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unfinished_basket_id')
                ->constrained('unfinished_baskets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->integer('count');
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unfinished_baskets_products');
    }
}
