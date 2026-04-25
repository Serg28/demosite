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
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->text('applicable_product_codes')->nullable()->after('use_for_discount_cards');
        });

        Schema::create('promo_code_product', function (Blueprint $table) {
            $table->unsignedBigInteger('promo_code_id');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
            $table->string('product_code');
            $table->foreign('product_code')->references('code')->on('products')->onDelete('cascade');
            $table->primary(['promo_code_id', 'product_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_code_product');

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn('applicable_product_codes');
        });
    }
};
