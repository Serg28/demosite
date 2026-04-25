<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockBannersSliderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_banners_slider', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('block_id')->index()->nullable();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->json('picture');
            $table->json('link')->nullable();
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_target_blank');
            $table->integer('priority');
            $table->foreign('block_id')
                ->on('blocks')
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
        Schema::dropIfExists('block_banners_slider');
    }
}
