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
        Schema::create('warehouse_delivery_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedInteger('delivery_id');
            $table->integer('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('days_to_delivery')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');

            $table->index('warehouse_id');
            $table->index('delivery_id');
            $table->index(['warehouse_id', 'days_to_delivery']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_delivery_schedules');
    }
};
