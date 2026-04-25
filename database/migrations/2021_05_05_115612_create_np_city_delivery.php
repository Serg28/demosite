<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNpCityDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('np_city_delivery', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('np_city_id')->index();
            $table->unsignedInteger('delivery_id')->index();

            $table->foreign('np_city_id')
                ->on('np_cities')
                ->references('id');

            $table->foreign('delivery_id')
                ->on('deliveries')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('np_city_delivery');
    }
}
