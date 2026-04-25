<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->increments('id');
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('picture')->nullable();
            $table->integer('priority')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->string('id_1c');
            $table->integer('characteristic_option_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brands');
    }
}
