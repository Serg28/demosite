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
        Schema::create('block_buttons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->index();
            $table->string('type')->default('button');
            $table->string('className')->default('main-btn main-btn--red');
            $table->json('subject')->nullable();
            $table->json('text');
            $table->json('tooltip')->nullable();
            $table->json('link')->nullable();
            $table->string('component');
            $table->string('parametr');
            $table->tinyInteger('recaptcha')->default(0);
            $table->tinyInteger('is_active');
            $table->integer('priority')->default(0);
            $table->timestamps();
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
        Schema::dropIfExists('block_buttons');
    }
};
