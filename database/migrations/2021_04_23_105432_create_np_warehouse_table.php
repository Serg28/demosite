<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNpWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('np_warehouse', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->unsignedBigInteger('np_city_id');

            $table->foreign('np_city_id')
                ->on('np_cities')
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
        Schema::dropIfExists('np_warehouse');
    }
}
