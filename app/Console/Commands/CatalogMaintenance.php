<?php

namespace App\Console\Commands;

use App\Jobs\MaintenanceCatalog;
use Exception;
use Illuminate\Console\Command;

/**
 * Класс CatalogMaintenance
 *
 * Эта команда отвечает за запуск фоновой задачи для процесса полного обслуживания каталога товаров.
 *
 * @package App\Console\Commands
 */
class CatalogMaintenance extends Command
{
    protected $signature = 'catalog:maintenance';

    protected $description = 'Perform maintenance tasks on catalog';

    /**
     * Обработать процесс обслуживания для категорий.
     *
     * Этот метод отправляет задачу MaintenanceCatalog для выполнения
     * задач по обслуживанию каталога товаров в фоновом режиме. Также обрабатывает любые
     * исключения, которые могут возникнуть при отправке задачи, и
     * предоставляет соответствующий вывод в консоль.
     *
     * @return int Статус завершения команды
     */
    public function handle(): int
    {
        try {
            // Отправить фоновую задачу для обслуживания каталога
            MaintenanceCatalog::dispatch();

            // Вывести сообщение об успешном запуске в консоль
            $this->info('Фоновый процесс обслуживания каталога запущен');

            // Вернуть код статуса успешного завершения
            return Command::SUCCESS;
        } catch (Exception $е) {
            // Вывести сообщение об ошибке в консоль
            $this->error('Произошла ошибка при обслуживании каталога: ' . $е->getMessage());

            // Вернуть код статуса неудачного завершения
            return Command::FAILURE;
        }
    }
}
