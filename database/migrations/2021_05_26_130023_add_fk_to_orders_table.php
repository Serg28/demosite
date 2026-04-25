<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_pickup_point_id')->index()->nullable();
            $table->unsignedBigInteger('np_warehouse_id')->index()->nullable();

            $table->foreign('delivery_pickup_point_id')
                ->on('delivery_pickup_points')
                ->references('id')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('np_warehouse_id')
                ->on('np_warehouse')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_pickup_point_id']);
            $table->dropIndex(['delivery_pickup_point_id']);
            $table->dropColumn(['delivery_pickup_point_id']);

            $table->dropForeign(['np_warehouse_id']);
            $table->dropIndex(['np_warehouse_id']);
            $table->dropColumn(['np_warehouse_id']);
        });
    }
}
