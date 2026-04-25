<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCharacteristicOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_options', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->increments('id');
            $table->unsignedInteger('characteristic_id')->index();
            $table->json('title');
            $table->json('description');
            $table->tinyInteger('is_active');
            $table->integer('priority')->default(0);

            $table->foreign('characteristic_id')
                    ->references('id')
                    ->on('characteristics')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('characteristic_options');
    }
}
