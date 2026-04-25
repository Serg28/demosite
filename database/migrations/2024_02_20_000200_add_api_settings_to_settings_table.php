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
        //Закомментировано в связи с импортом в дампе
        /*Schema::table('settings', function (Blueprint $table) {
            \DB::table('settings')->insert([
                'type' => 'text',
                'title' => 'Разрешенные IP для подключения к API',
                'slug' => 'site-api-allowed-ip',
                'value' => '',
                'group' => 'site_api',
            ]);
            \DB::table('settings')->insert([
                'type' => 'text',
                'title' => 'Ключ Bearer API',
                'slug' => 'site-api-bearer-key',
                'value' => '',
                'group' => '',
            ]);
            \DB::table('settings')->insert([
                'type' => 'text',
                'title' => 'Email пользователей для доступа к документации api',
                'slug' => 'site-api-bearer-key',
                'value' => '',
                'group' => 'site-api-docs-allowed-emails',
            ]);
        });*/
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
};
