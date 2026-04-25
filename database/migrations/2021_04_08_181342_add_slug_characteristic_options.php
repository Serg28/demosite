<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugCharacteristicOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->string('slug')->unique();
        });

        Schema::table('characteristics', function (Blueprint $table) {
            $table->string('slug')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });

        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
}
