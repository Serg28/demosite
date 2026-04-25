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
        Schema::create('block_facts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->index();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('short_description')->nullable();
            $table->string('picture')->nullable();
            $table->integer('priority')->default(0);
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
        Schema::dropIfExists('block_facts');
    }


};
