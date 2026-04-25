<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characteristics', function (Blueprint $table) {
            // Добавляем виртуальные столбцы для каждого языка
            $languages = ['ru', 'ua', 'en', 'pl']; // Замените на свои языки
            foreach ($languages as $language) {
                $table->string($language . '_url')->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(url, '$.\"$language\"'))");
                $table->index($language . '_url'); // Создаем индекс для виртуального столбца
            }
        });

        // Создаем триггеры для обновления виртуальных столбцов при создании или обновлении записи
        /*$languages = ['ru', 'ua', 'en', 'pl']; // Замените на свои языки
        foreach ($languages as $language) {
            DB::unprepared("
                CREATE TRIGGER before_characteristics_{$language}_url_insert BEFORE INSERT ON characteristics
                FOR EACH ROW
                BEGIN
                    SET NEW.{$language}_url = JSON_UNQUOTE(JSON_EXTRACT(NEW.url, '$.\"$language\"'));
                END
            ");

            DB::unprepared("
                CREATE TRIGGER before_characteristics_{$language}_url_update BEFORE UPDATE ON characteristics
                FOR EACH ROW
                BEGIN
                    SET NEW.{$language}_url = JSON_UNQUOTE(JSON_EXTRACT(NEW.url, '$.\"$language\"'));
                END
            ");
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Удаляем триггеры для обновления виртуальных столбцов
        /*$languages = ['ru', 'ua', 'en', 'pl']; // Замените на свои языки
        foreach ($languages as $language) {
            DB::unprepared("DROP TRIGGER IF EXISTS before_characteristics_{$language}_url_insert");
            DB::unprepared("DROP TRIGGER IF EXISTS before_characteristics_{$language}_url_update");
        }*/

        // Удаляем виртуальные столбцы и индексы
        Schema::table('characteristics', function (Blueprint $table) {
            $languages = ['ru', 'ua', 'en', 'pl']; // Замените на свои языки
            foreach ($languages as $language) {
                $table->dropIndex("{$language}_url");
                $table->dropColumn($language . '_url');
            }
        });
    }


};
