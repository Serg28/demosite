<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSettingViber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Закомментировано в связи с импортом в дампе
        /*DB::table('settings')->insert([
            'type' => 'text',
            'title' => 'Link to viber',
            'slug' => 'link-to-viber',
            'value' => 'https://www.viber.com/en/',
            'group' => 'social',
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
