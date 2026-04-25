<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->string('code')->nullable();
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->integer('price')->index();
            $table->integer('price_old');
            $table->string('status')->nullable();
            $table->string('picture')->nullable();
            $table->text('other_pictures')->nullable();
            $table->text('link_to_youtube')->nullable();
            $table->tinyInteger('is_active')->index();

            $table->timestamps();
            //$table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
