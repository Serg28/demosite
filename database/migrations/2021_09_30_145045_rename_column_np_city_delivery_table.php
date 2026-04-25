<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnNpCityDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('np_city_delivery', function (Blueprint $table) {
            $table->renameColumn('np_city_id', 'city_id');
        });

        Schema::table('np_warehouse', function (Blueprint $table) {
            $table->renameColumn('np_city_id', 'city_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('np_city_delivery', function (Blueprint $table) {
            $table->renameColumn('city_id', 'np_city_id');
        });

        Schema::table('np_warehouse', function (Blueprint $table) {
            $table->renameColumn('city_id', 'np_city_id');
        });
    }
}
