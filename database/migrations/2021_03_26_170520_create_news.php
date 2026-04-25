<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->string('slug');
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->string('picture')->nullable();
            $table->tinyInteger('is_active');
            //$table->integer('count_views')->default(0);
            //$table->integer('user_id')->nullable();
            $table->integer('tree_id')->nullable();

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
        Schema::dropIfExists('news');
    }
}
