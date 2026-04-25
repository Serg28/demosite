<?php

namespace App\Http\ViewComposers;

use App\Models\Product;
use Illuminate\View\View;

/**
 * Композитор для загрузки схожих товаров для страницы.
 *
 * Этот композитор выполняет запрос к базе данных для получения товаров, схожих по цене и категории с текущим товаром.
 * Товары фильтруются по цене и категории, а также исключаются товары, которые являются текущим (по идентификатору).
 * Запрос кэшируется на 5 минут для улучшения производительности.
 */
class SimilarProductsComposer
{
    /**
     * Композитор для передачи схожих товаров в представление.
     *
     * @param \Illuminate\View\View $view Представление, в которое передаются данные.
     *
     * @return void
     */
    public function compose(View $view): void
    {
        // Получаем базовую цену текущего товара и рассчитываем диапазон цен
        $basePrice = $view->page->price;
        $priceRange = $basePrice * 0.1; // 10% от цены

        // Строим запрос для получения схожих товаров
        $query = Product::orderBy('count_views', 'desc') // Сортировка по количеству просмотров
        ->where('category_id', $view->page->category_id) // Фильтрация по категории текущего товара
        ->where('id', '!=', $view->page->id) // Исключаем текущий товар из результатов
        ->cardFields() // Добавляем дополнительные поля для карточки товара
        ->active() // Фильтрация по активным товарам
        ->take(8) // Ограничение на 8 товаров
        ->remember(5) // Кэширование результата на 5 минут
        ->cacheTags(['products']); // Теги для кэширования

        // Применяем фильтрацию по цене, если цена товара больше 0
        if ($basePrice > 0) {
            $query->whereBetween('price', [$basePrice - $priceRange, $basePrice + $priceRange]);
        }

        // Получаем схожие товары
        $similarProducts = $query->get();

        // Передаем схожие товары в представление
        $view->with(compact('similarProducts'));
    }
}
