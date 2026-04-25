<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryPickupPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_pickup_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('delivery_id')->index();
            $table->json('title')->nullable();
            $table->json('address')->nullable()->default(null);
            $table->integer('priority')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_pickup_points');
    }
}
