<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColunmToCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();

            $table->json('origin')->nullable();

            $table->foreign('type_id')->on('settlements')->references('id')->onDelete('restrict')->onDelete('cascade');
            $table->foreign('region_id')->on('regions')->references('id')->onDelete('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn(['type_id']);

            $table->dropForeign(['region_id']);
            $table->dropColumn(['region_id']);

            $table->dropColumn('origin');
        });
    }
}
