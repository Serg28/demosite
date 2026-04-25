<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('news_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_article');
//            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('news_products');
    }
};