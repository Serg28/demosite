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
        Schema::create('tb_tree_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tree_id')->index();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lft')->index();
            $table->integer('rgt')->index();
            $table->integer('depth')->default(0);
            $table->unsignedInteger('menu_id')->nullable()->index();
            $table->string('menu_type')->nullable();
            $table->json('url')->nullable();
            $table->tinyInteger('is_target_blank')->default(0);
            $table->index(['menu_id', 'menu_type']);
            $table->foreign('tree_id')->on('tb_tree')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_tree_menus');
    }
};
