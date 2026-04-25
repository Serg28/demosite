<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeestWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meest_warehouse', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->unsignedBigInteger('city_id');

            $table->foreign('city_id')
                ->on('cities')
                ->references('id')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meest_warehouse');
    }
}
