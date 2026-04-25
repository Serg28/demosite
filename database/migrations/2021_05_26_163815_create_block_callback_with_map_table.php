<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockCallbackWithMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_callback_with_map', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->index();
            $table->json('title')->nullable();
            $table->json('map')->nullable();
            $table->foreign('block_id')->on('blocks')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_callback_with_map');
    }
}
