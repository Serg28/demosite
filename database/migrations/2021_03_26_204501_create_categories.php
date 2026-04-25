<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable()->index();
                $table->integer('lft')->index();
                $table->integer('rgt')->index();
                $table->integer('depth')->default(0);
                $table->json('title')->nullable();
                $table->string('slug');
                $table->string('picture');
                $table->tinyInteger('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
