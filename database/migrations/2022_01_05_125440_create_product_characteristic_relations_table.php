<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCharacteristicRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('characteristic_id');
            $table->string('title', 255)->nullable();

            $table->foreign('characteristic_id')
                ->references('id')
                ->on('characteristics')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characteristic_relations');
    }
}
