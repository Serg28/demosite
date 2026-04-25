<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductJoinBlocksTable extends Migration
{
    public function up(): void
    {
        Schema::create('product_join_blocks', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->json('title');
            $table->integer('priority');

            $table->foreign('product_id')->on('products')->references('id')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_join_blocks');
    }
}
