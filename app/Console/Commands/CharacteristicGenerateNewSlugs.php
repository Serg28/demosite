<?php

namespace App\Console\Commands;

use App\Models\Characteristic;
use Illuminate\Console\Command;

class CharacteristicGenerateNewSlugs extends Command
{
    protected $signature = 'characteristic:generate-new-slugs';
    protected $description = 'Generate unique slugs for characteristics where slug is empty';

    public function handle(): void
{
    try {
        // Получаем записи, у которых slug пустой
        $characteristics = Characteristic::whereNull('slug')->orWhere('slug', '')->get();

        $updatedCount = 0;

        foreach ($characteristics as $characteristic) {
            // Генерируем уникальный slug
            $characteristic->slug = $this->generateUniqueSlug($characteristic);

            // Обновляем запись в базе данных
            $characteristic->saveQuietly(); // Сохраняем без вызова событий

            $updatedCount++;
        }

        $this->info("Successfully updated $updatedCount characteristics.");
    } catch (\Exception $e) {
        $this->error('An error occurred while updating characteristics: ' . $e->getMessage());
    }
}

    /**
     * Генерирует уникальный slug для заданного характеристики.
     *
     * @param Characteristic $characteristic
     * @return string
     */
    private function generateUniqueSlug(Characteristic $characteristic): string
    {
        $slug = $characteristic->getSlug('', 'a-z0-9'); // Получаем текущий slug или базовый slug

        // Проверяем уникальность slug'а
        $count = Characteristic::where('slug', $slug)
            ->where('id', '!=', $characteristic->id)
            ->count();

        // Если есть дубли, добавляем суффикс с номером
        return ($count > 0) ? $slug . '-' . ($count + 1) : $slug;
    }
}
