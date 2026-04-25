<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Проверяем существование таблицы
        if (!Schema::hasTable('settings')) {
            // Создаем таблицу, если её нет
            Schema::create('settings', function (Blueprint $table) {
                $table->collation = 'utf8_general_ci';
                $table->charset = 'utf8';

                $table->increments('id');
                $table->integer('type');
                $table->string('title');
                $table->string('slug')->index();
                $table->json('value')->nullable();
                $table->string('group_type');
            });
        } else {
            // Обновляем структуру таблицы, если она уже существует
            /*Schema::table('settings', function (Blueprint $table) {
                $table->collation = 'utf8_general_ci';
                $table->charset = 'utf8';

                $table->increments('id');
                $table->integer('type');
                $table->string('title');
                $table->string('slug')->index();
                $table->json('value')->nullable();
                $table->string('group_type');
            });*/
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Вы можете использовать dropIfExists в методе down, если это необходимо
        // Например: Schema::dropIfExists('settings');
    }
}
