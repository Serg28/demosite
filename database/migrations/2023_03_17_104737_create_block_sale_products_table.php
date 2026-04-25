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
        Schema::create('block_sale_products', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable();
            $table->string('product_code')->nullable()->index();
            $table->integer('priority')->default(0);

            $table->foreign('block_id')->references('id')
                ->on('blocks');
            //->onDelete('cascade');
            //->onUpdate('cascade');

            //$table->foreign('product_code')->references('code')
            //  ->on('products');
            //->onDelete('cascade');
            //->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_sale_products');
    }
};
