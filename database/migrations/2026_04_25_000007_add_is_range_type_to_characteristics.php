<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Создаем characteristics таблицу если её еще нет
        if (!Schema::hasTable('characteristics')) {
            Schema::create('characteristics', function (Blueprint $table) {
                $table->id();
                $table->json('title');
                $table->string('slug')->unique()->nullable();
                $table->boolean('is_range_type')->default(false)->comment('Включить слайдер диапазона');
                $table->boolean('is_active')->default(true);
                $table->integer('priority')->default(0);
                $table->timestamps();
            });
        } else {
            // Если таблица существует, добавляем колонку если её нет
            Schema::table('characteristics', function (Blueprint $table) {
                if (!Schema::hasColumn('characteristics', 'is_range_type')) {
                    $table->boolean('is_range_type')->default(false)->after('slug')
                        ->comment('Включить слайдер диапазона');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            if (Schema::hasColumn('characteristics', 'is_range_type')) {
                $table->dropColumn('is_range_type');
            }
        });
    }
};
