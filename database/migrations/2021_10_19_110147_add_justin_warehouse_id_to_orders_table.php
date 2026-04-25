<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJustinWarehouseIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('justin_warehouse_id')->index()->nullable();

            $table->foreign('justin_warehouse_id')
                ->on('justin_warehouse')
                ->references('id')
                ->onDelete('cascade');
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
            $table->dropForeign(['justin_warehouse_id']);
            $table->dropIndex(['justin_warehouse_id']);
            $table->dropColumn(['justin_warehouse_id']);
        });
    }
}
