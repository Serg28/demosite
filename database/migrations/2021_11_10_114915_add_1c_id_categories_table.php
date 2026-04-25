<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add1cIdCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('id_1c', 36)->nullable()->index();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('id_1c', 36)->nullable()->index();
        });
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->string('id_1c', 36)->nullable()->index();
        });
        Schema::table('characteristics', function (Blueprint $table) {
            $table->string('id_1c', 36)->nullable()->index();
        });
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('id_1c', 36)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
    }
}
