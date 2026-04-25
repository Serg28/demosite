<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class ProcessGoogleCategories
 *
 * Эта команда обрабатывает категории Google из файлов на русском, английском и украинском языках.
 *
 * Использование:
 * php artisan process:google-categories {ruFile} {enFile} {uaFile}
 *
 * Параметры:
 * - ruFile: Путь к файлу с категориями на русском языке.
 * - enFile: Путь к файлу с категориями на английском языке.
 * - uaFile: Путь к файлу с категориями на украинском языке.
 *
 * Команда создает временную таблицу для хранения категорий, затем заполняет ее данными
 * из файлов, и в конечном итоге переносит данные в основную таблицу с созданием
 * JSON поля, содержащего названия категорий на всех трех языках.
 *
 * Файлы можно взать здесь:
 * https://www.google.com/basepages/producttype/taxonomy-with-ids.ru-RU.txt
 * https://www.google.com/basepages/producttype/taxonomy-with-ids.uk-UA.txt
 * https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt
 *
 * Сохранить их как файлы в папке на хостинге и затем указать их как аргументы в команде
 */

class ProcessGoogleCategories extends Command
{
    // Название команды и ее описание
    protected $signature = 'process:google-categories {ruFile} {enFile} {uaFile}';
    protected $description = 'Process Google categories from RU, EN, and UA files, insert into temporary table, create JSON field, and transfer data to the main table';

    public function handle()
    {
        // Получаем имена файлов
        $ruFile = $this->argument('ruFile');
        $enFile = $this->argument('enFile');
        $uaFile = $this->argument('uaFile');

        // Проверяем, существуют ли файлы
        if (!File::exists($ruFile) || !File::exists($enFile) || !File::exists($uaFile)) {
            $this->error('Один или несколько файлов не найдены.');
            return 1;
        }

        // Создание временной таблицы google_categories_tmp
        DB::statement("
            CREATE TABLE IF NOT EXISTS google_categories_tmp (
                id INT PRIMARY KEY,
                title_ru TEXT,
                title_en TEXT,
                title_ua TEXT
            );
        ");
        $this->info('Временная таблица google_categories_tmp создана.');

        // Очистка временной таблицы
        DB::table('google_categories_tmp')->truncate();
        $this->info('Таблица google_categories_tmp очищена.');

        // Чтение и вставка данных из RU файла
        $this->insertDataFromFile($ruFile, 'title_ru');

        // Чтение и вставка данных из EN файла
        $this->insertDataFromFile($enFile, 'title_en');

        // Чтение и вставка данных из UA файла
        $this->insertDataFromFile($uaFile, 'title_ua');

        // Обновление колонки title как JSON
        DB::statement("
            UPDATE google_categories_tmp
            SET title = JSON_OBJECT(
                'ru', title_ru,
                'en', title_en,
                'ua', title_ua
            );
        ");
        $this->info('JSON колонка title обновлена.');

        // Очистка основной таблицы и перенос данных
        DB::statement("TRUNCATE TABLE google_categories;");
        DB::statement("
            INSERT INTO google_categories (id, title)
            SELECT id, title
            FROM google_categories_tmp;
        ");
        $this->info('Данные успешно перенесены в таблицу google_categories.');

        return 0;
    }

    /**
     * Функция для чтения файла и вставки данных в соответствующую колонку
     *
     * @param string $filePath Путь к файлу для чтения
     * @param string $column Название колонки, в которую будут вставлены данные
     *
     * @return void
     */
    private function insertDataFromFile($filePath, $column)
    {
        $file = fopen($filePath, 'r');

        if ($file) {
            while (($line = fgets($file)) !== false) {
                // Пропускаем пустые строки и строки с комментариями
                if (trim($line) === '' || strpos($line, '#') === 0) {
                    continue;
                }

                // Разделяем строку по знаку " - "
                $parts = explode(' - ', $line, 2);
                if (count($parts) == 2) {
                    $id = trim($parts[0]);
                    $title = trim($parts[1]);

                    // Вставляем или обновляем данные
                    DB::table('google_categories_tmp')
                        ->updateOrInsert(
                            ['id' => $id],
                            [$column => $title]
                        );
                }
            }

            fclose($file);
            $this->info("Данные из файла {$filePath} успешно добавлены в колонку {$column}.");
        } else {
            $this->error("Не удалось открыть файл: {$filePath}");
        }
    }
}
