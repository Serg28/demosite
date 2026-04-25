<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricelistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricelists', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('price')->nullable();
            $table->json('price_old')->nullable();
            $table->tinyInteger('is_active');
            $table->integer('priority');

            $table->unsignedBigInteger('pricelist_rubric_id');

            $table->foreign('pricelist_rubric_id')
                ->on('block_pricelist_rubrics')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricelists');
    }
}
