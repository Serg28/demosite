<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorAndShowBtnToBlockBannersSliderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('block_banners_slider', function (Blueprint $table) {
            $table->string('color')->nullable();
            $table->tinyInteger('is_show_btn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('block_banners_slider', function (Blueprint $table) {
            $table->dropColumn([
                'color', 'is_show_btn',
            ]);
        });
    }
}
