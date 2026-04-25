<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUkrposhtaWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ukrposhta_warehouse', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('postcode');

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
        Schema::dropIfExists('ukrposhta_warehouse');
    }
}
