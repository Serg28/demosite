<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductJoinProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_join_products', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->id();
            $table->unsignedBigInteger('product_join_block_id')->index();
            $table->unsignedBigInteger('product_id')->index();

            $table->foreign('product_join_block_id')->on('product_join_blocks')->references('id')->onDelete('cascade');
            $table->foreign('product_id')->on('products')->references('id')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_join_products');
    }
}
