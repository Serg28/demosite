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
        //$table->integer('priority');
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->integer('priority')->index()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->dropColumn(['priority']);
        });
    }
};
