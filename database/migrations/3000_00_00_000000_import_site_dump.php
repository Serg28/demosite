<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Укажите путь к вашему SQL файлу
        $path = database_path('snapshots/dump.sql');
        if (file_exists($path)) {
            // Выполнение SQL файла
            DB::unprepared(file_get_contents($path));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};


