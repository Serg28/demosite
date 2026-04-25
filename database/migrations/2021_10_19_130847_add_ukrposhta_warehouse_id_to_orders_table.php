<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUkrposhtaWarehouseIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('ukrposhta_warehouse_id')->index()->nullable();

            $table->foreign('ukrposhta_warehouse_id')
                ->on('ukrposhta_warehouse')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['ukrposhta_warehouse_id']);
            $table->dropIndex(['ukrposhta_warehouse_id']);
            $table->dropColumn(['ukrposhta_warehouse_id']);
        });
    }
}
