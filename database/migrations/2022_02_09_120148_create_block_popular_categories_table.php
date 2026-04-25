<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockPopularCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_popular_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('priority')->default(0);

            $table->foreign('block_id')->references('id')
                ->on('blocks')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('category_id')->references('id')
                ->on('categories')
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
        Schema::dropIfExists('block_popular_categories');
    }
}
