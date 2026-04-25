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
        Schema::table('products', function (Blueprint $table) {
            $table->string('has_mono_payparts');
            $table->string('has_privat_payparts');
            $table->string('privat_payparts_count');
            $table->string('mono_payparts_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_mono_payparts');
            $table->dropColumn('has_privat_payparts');
            $table->dropColumn('privat_payparts_count');
            $table->dropColumn('mono_payparts_count');
        });
    }
};
