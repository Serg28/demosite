<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTreeBlocksHitProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_hit_products', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('priority')->default(0);

            $table->foreign('block_id')->references('id')
                ->on('blocks')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('product_id')->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_hit_products');
    }
}
