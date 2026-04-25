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
        Schema::create('menu_sidebar', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lft')->index();
            $table->integer('rgt')->index();
            $table->integer('depth')->default(0);
            $table->json('title')->nullable();
            $table->json('url')->nullable();
            $table->string('picture');
            $table->tinyInteger('is_target_blank')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->unsignedInteger('menu_id')->nullable()->index();
            $table->string('menu_type')->nullable();

            $table->index(['menu_id', 'menu_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_sidebar');
    }
};