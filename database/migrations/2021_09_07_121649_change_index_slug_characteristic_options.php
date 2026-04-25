<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIndexSlugCharacteristicOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->dropUnique('characteristic_options_slug_unique');
            $table->index('slug');
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
            $table->dropIndex(['slug']);
            $table->unique('slug');
        });
    }
}
