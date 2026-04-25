<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeestWarehouseIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('meest_warehouse_id')->index()->nullable();

            $table->foreign('meest_warehouse_id')
                ->on('meest_warehouse')
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
            $table->dropForeign(['meest_warehouse_id']);
            $table->dropIndex(['meest_warehouse_id']);
            $table->dropColumn(['meest_warehouse_id']);
        });
    }
}
