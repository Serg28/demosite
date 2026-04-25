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
        Schema::create('information_board', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('text')->nullable();
            $table->json('tooltip')->nullable();
            $table->json('link')->nullable();
            $table->string('picture')->nullable();
            $table->string('background')->nullable();
            $table->string('color')->nullable();
            $table->json('style');
            $table->json('className')->nullable();
            $table->tinyInteger('is_show_btn')->default(1);
            $table->tinyInteger('is_active')->default(0);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('information_board');
    }
};
