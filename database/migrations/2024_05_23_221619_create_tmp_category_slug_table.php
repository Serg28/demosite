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
        Schema::create('tmp_category_slug', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id')->index();
            $table->string('id_1c', 36)->nullable()->index();
            $table->json('title')->nullable();
            $table->string('slug');
            $table->json('url')->nullable();
            $table->tinyInteger('is_active');

            //$table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict')->onUpdate('cascade');

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
        Schema::dropIfExists('tmp_category_slug');
    }
};
