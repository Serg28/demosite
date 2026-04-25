<?php

namespace App\Listeners\Category;

use App\Events\CategorySaved;
use App\Models\Category;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CategoryUniqueSlug implements ShouldQueue
{
    use SerializesModels;
    /**
     * Имя соединения, на которое должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $connection = 'redis';

    /**
     * Имя очереди, в которую должно быть отправлено задание.
     *
     * @var string|null
     */
    public ?string $queue = 'high';

    public function handle(CategorySaved $event): void
    {
            // Если slug не установлен или пуст, генерируем его на основе имени
            if (!$event->category->slug) {
                $event->category->slug = Str::slug($event->category->name);
            }

            // Проверяем, есть ли категория с таким slug
            $count = Category::where('slug', $event->category->slug)->where('id', '!=', $event->category->id)->count();

            // Если уже существует категория с таким slug
            if ($count > 0) {
                // Добавляем к slug уникальный идентификатор записи
                $event->category->slug .= '-' . $event->category->id;
            }
    }
}