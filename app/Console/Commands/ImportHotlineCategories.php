<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Класс ImportHotlineCategories
 *
 * Эта команда Laravel позволяет импортировать категории Hotline из CSV файла.
 * Данные сразу вставляются в базу данных, предварительно очищая таблицу.
 *
 * Пример использования:
 * php artisan import:hotline-categories your_file.csv
 *
 * Файл с рубрикатором категорий здесь: https://hotline.ua/download/hotline/hotline_tree_uk.csv
 * По-умолчанию его надо сохранить в папку public
 *
 * @package App\Console\Commands
 */
class ImportHotlineCategories extends Command
{
    protected $signature = 'import:hotline-categories {file}';
    protected $description = 'Импортирует категории Hotline из CSV файла';

    public function handle()
    {
        $filename = public_path($this->argument('file'));

        if (!file_exists($filename)) {
            $this->error('Файл не существует.');
            return;
        }

        DB::statement('DROP TABLE IF EXISTS hotline_categories');
        DB::statement('CREATE TABLE IF NOT EXISTS hotline_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name JSON NOT NULL,
            title JSON NOT NULL
        )');

        $file = file($filename);
        $breadcrumbs = [];
        foreach ($file as $line) {
            $line = trim($line);
            $depth = substr_count($line, ';');
            $breadcrumbs = array_slice($breadcrumbs, 0, $depth);
            $breadcrumbs[] = trim($line, ';');
            $name = end($breadcrumbs);
            $title = implode(' > ', $breadcrumbs);

            $nameJson = json_encode([
                'en' => $name,
                'ru' => $name,
                'ua' => $name,
            ]);

            $titleJson = json_encode([
                'en' => $title,
                'ru' => $title,
                'ua' => $title,
            ]);

            DB::statement("INSERT INTO hotline_categories (name, title) VALUES (" . DB::getPdo()->quote($nameJson) . ", " . DB::getPdo()->quote($titleJson) . ")");
        }

        $this->info('Данные успешно импортированы.');
    }
}