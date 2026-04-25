<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJustinWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('justin_warehouse', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('uuid');

            $table->foreign('city_id')
                ->on('cities')
                ->references('id')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('justin_warehouse');
    }
}
