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
        Schema::table('promotions', function (Blueprint $table) {
            $table->json('url')->after('slug')->nullable();
            // Добавляем виртуальные столбцы для каждого языка
            $languages = ['ru', 'ua', 'en']; // Замените на свои языки
            foreach ($languages as $language) {
                $table->string($language . '_url')->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(url, '$.\"$language\"'))");
                $table->index($language . '_url'); // Создаем индекс для виртуального столбца
            }
        });
        DB::statement('
            CREATE TRIGGER before_promotion_insert
            BEFORE INSERT ON promotions
            FOR EACH ROW
            BEGIN
                IF NEW.url = \'{"ua":"","ru":""}\' THEN
                    SET NEW.url = NULL;
                END IF;
            END
        ');

        DB::statement('
            CREATE TRIGGER before_promotion_update
            BEFORE UPDATE ON promotions
            FOR EACH ROW
            BEGIN
                IF NEW.url = \'{"ua":"","ru":""}\' THEN
                    SET NEW.url = NULL;
                END IF;
            END
        ');
        // Создаем триггеры для обновления виртуальных столбцов при создании или обновлении записи
        /*$languages = ['ru', 'ua', 'en']; // Замените на свои языки
        foreach ($languages as $language) {
            DB::unprepared("
                CREATE TRIGGER before_promotion_{$language}_url_insert BEFORE INSERT ON promotions
                FOR EACH ROW
                BEGIN
                    SET NEW.{$language}_url = JSON_UNQUOTE(JSON_EXTRACT(NEW.url, '$.\"$language\"'));
                END
            ");

            DB::unprepared("
                CREATE TRIGGER before_promotion_{$language}_url_update BEFORE UPDATE ON promotions
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
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('url');

            $languages = ['ru', 'ua', 'en']; // Замените на свои языки
            foreach ($languages as $language) {
                $table->dropIndex("{$language}_url");
                $table->dropColumn($language . '_url');
            }
        });
        DB::statement('DROP TRIGGER IF EXISTS before_promotion_insert');
        DB::statement('DROP TRIGGER IF EXISTS before_promotion_update');

        // Удаляем триггеры для обновления виртуальных столбцов
        /*$languages = ['ru', 'ua', 'en']; // Замените на свои языки
        foreach ($languages as $language) {
            DB::unprepared("DROP TRIGGER IF EXISTS before_promotion_{$language}_url_insert");
            DB::unprepared("DROP TRIGGER IF EXISTS before_promotion_{$language}_url_update");
        }*/
    }
};
