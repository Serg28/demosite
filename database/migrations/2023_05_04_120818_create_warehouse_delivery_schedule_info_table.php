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
        Schema::create('warehouse_delivery_schedule_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('delivery_id')->index('delivery_id');
            $table->unsignedBigInteger('delivery_schedules_id')->index('delivery_schedules_id');
            $table->unsignedInteger('days_to_delivery');
            $table->json('description');
            $table->timestamps();
            $table->integer('priority');

            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');
            $table->foreign('delivery_schedules_id')->references('id')->on('delivery_schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_delivery_schedule_info');
    }
};
