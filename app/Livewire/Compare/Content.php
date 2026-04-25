<?php

namespace App\Livewire\Compare;

use App\Livewire\Compare\Count as CompareCount;
use App\Models\Characteristic;
use App\Models\ProductCharacteristicOption;
use App\Services\Compare;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\Component;

class Content extends Component
{
    public $categoryCounts = [];
    public $categories = [];
    public $products = [];
    public $groupedCharacteristics = [];
    public $showDifferences = false; // переменная для чекбокса
    public $category_id = false;


    public function mount(Request $request, Compare $compare)
    {
        // Получаем ID категории из запроса
        $this->category_id = $request->input('category');

        // Проверяем, есть ли товары для сравнения в сессии
        if (!session()->has('compare') || !$compare->count()) {
            // Если в сессии нет товаров для сравнения, отображаем сообщение о пустоте
            $this->products = collect();  // Пустая коллекция товаров
            $this->categories = collect(); // Пустая коллекция категорий
            return; // Прерываем выполнение, чтобы не делать редирект
        }

        // Если ID категории отсутствует, но есть товары для сравнения
        if (!$this->category_id) {
            // Получаем первую категорию из сессии и редиректим на неё
            $firstCategory = array_key_first(session()->get('compare'));
            return redirect()->route('compare', ['category' => $firstCategory]);
        }

        // Обновляем количество товаров по категориям
        $this->categoryCounts = $compare->countByCategory();

        // Проверяем, существует ли текущая категория и товары в ней
        if ($compare->isCategoryExists($this->category_id)) {
            // Загружаем товары для сравнения по категории
            $this->products = $compare->getProductsByIds($compare->getCompareSession()[$this->category_id]);
            $this->categories = $compare->getExistsCategories();
        } else {
            // Если текущая категория пуста, проверяем и редиректим
            $redirect = $this->checkAndRemoveEmptyCategory($compare);
            if ($redirect) {
                return $redirect;
            }
        }

        // Группируем характеристики товаров
        $this->groupCharacteristics();
    }

    public function groupCharacteristics()
    {
        $this->groupedCharacteristics = collect(); // Инициализация коллекции для сгруппированных характеристик

        // Собираем все характеристики из продуктов
        $allCharacteristics = $this->products->flatMap(fn($product) => $product->allCharacteristics()->get());

        // Фильтрация по отличиям, если включена опция "Только отличия"
        if ($this->showDifferences) {
            $allCharacteristics = $allCharacteristics
                ->groupBy('characteristic_id')
                ->filter(fn($characteristics) => $characteristics->count() === 1 || $characteristics->pluck('characteristic_option_id')->unique()->count() > 1)
                ->flatten();
        }

        // Если характеристик нет, возвращаем пустую коллекцию
        if ($allCharacteristics->isEmpty()) {
            return $this->groupedCharacteristics;
        }

        // Подготовка массива характеристик по продуктам
        $characteristicsByProduct = $allCharacteristics->groupBy('product_id')->mapWithKeys(function ($items, $productId) {
            return [
                $productId => $items->groupBy('characteristic_id')->mapWithKeys(function ($charItems) {
                    return [
                        $charItems->first()->characteristic_id => $charItems->mapWithKeys(fn($item) => [$item->characteristic_option_id => $item->t('option_title')]),
                    ];
                }),
            ];
        });

        // Уникальные характеристики по characteristic_id
        $uniqueCharacteristics = $allCharacteristics->unique('characteristic_id');

        // Группировка характеристик по group_id и сбор значений для продуктов
        $this->groupedCharacteristics = $uniqueCharacteristics
            ->groupBy('group_id')
            ->map(function ($group) use ($characteristicsByProduct) {
                $groupTitle = $group->first()->t('group_title') ?: __('Общие');

                // Группируем характеристики и подготавливаем значения для каждого продукта
                $characteristics = $group->map(function ($item) use ($characteristicsByProduct) {
                    $productValues = $this->products->map(fn($product) => $characteristicsByProduct[$product->id][$item->characteristic_id] ?? collect());

                    return [
                        'id' => $item->characteristic_id,
                        'title' => $item->t('characteristic_title'),
                        'products' => $productValues,
                    ];
                });

                return [
                    'group_title' => $groupTitle,
                    'characteristics' => $characteristics,
                ];
            });

        $this->dispatch('compare-slider-updated');
        return $this->groupedCharacteristics;
    }


    //не оптимизированная версия, но рабочая на 100%, удалить по завершению всех тестов
    public function _groupCharacteristics()
    {
        try {
            $this->groupedCharacteristics = collect(); // Инициализируем коллекцию для сгруппированных характеристик

            // Получаем все характеристики из продуктов
            $allCharacteristics = collect($this->products)->flatMap(function ($product) {
                return $product->allCharacteristics()->get(); // Получаем все характеристики у продукта
            });

            // Если чекбокс "Тільки відмінності" активен
            if ($this->showDifferences) {
                // Группировка характеристик по characteristic_id
                $groupedByCharacteristic = $allCharacteristics->groupBy('characteristic_id');
                // Фильтрация: Оставляем характеристики, которые нужно сохранить
                $filteredCharacteristics = $groupedByCharacteristic->filter(function ($characteristics, $characteristicId) {
                    // Собираем все значения option_id для данной характеристики
                    $optionValues = $characteristics->pluck('characteristic_option_id');
                    // Уникальные значения
                    $uniqueCount = $optionValues->unique()->count();
                    // Условие: характеристика остается, если
                    // 1. Товаров у характеристики = 1
                    // 2. Либо товаров у характеристики > 1 и уникальных значений > 1
                    return $characteristics->count() === 1 || ($characteristics->count() > 1 && $uniqueCount > 1);
                });
                // Преобразуем фильтрованные характеристики обратно для использования дальше
                $allCharacteristics = $filteredCharacteristics->flatten(); // Преобразуем в обычную коллекцию
            }

            // Проверка на наличие характеристик
            if ($allCharacteristics->isEmpty()) {
                return $this->groupedCharacteristics; // Возвращаем пустую коллекцию, если нет характеристик
            }
            // Создаем массив для хранения характеристик по продуктам
            $characteristicsByProduct = [];
            $allCharacteristics->each(function ($item) use (&$characteristicsByProduct) {
                $productId = $item->product_id;
                $characteristicId = $item->characteristic_id;
                $characteristicOptionId = $item->characteristic_option_id;
                $optionTitle = $item->t('option_title'); // Заголовок опции

                // Инициализируем массив для товара, если его еще нет
                if (!isset($characteristicsByProduct[$productId])) {
                    $characteristicsByProduct[$productId] = [];
                }
                // Инициализируем массив для характеристики, если ее еще нет
                if (!isset($characteristicsByProduct[$productId][$characteristicId])) {
                    $characteristicsByProduct[$productId][$characteristicId] = [];
                }

                // Записываем характеристику и ее значение по option_id
                $characteristicsByProduct[$productId][$characteristicId][$characteristicOptionId] = $optionTitle;
            });

            // Получаем уникальные характеристики по characteristic_id
            $uniqueCharacteristics = $allCharacteristics->unique('characteristic_id');

            // Группировка характеристик по group_id
            $this->groupedCharacteristics = $uniqueCharacteristics->groupBy('group_id')
                ->map(function ($group) use ($characteristicsByProduct) {
                    $groupTitle = $group->first()->t('group_title');
                    if (empty($groupTitle)) { $groupTitle = __('Общие'); }
                    return [
                        'group_title' => $groupTitle, // Название группы
                        'characteristics' => $group->map(function ($item) use ($characteristicsByProduct) {
                            $productValues = []; // Массив для значений по продуктам

                            // Проходим по каждому продукту и получаем значение характеристики
                            foreach ($this->products as $product) {
                                $productId = $product->id;
                                $characteristicId = $item->characteristic_id;

                                // Проверяем, есть ли данные для данной характеристики у данного товара
                                if (isset($characteristicsByProduct[$productId][$characteristicId])) {
                                    $productValues[] = $characteristicsByProduct[$productId][$characteristicId];
                                }
                            }

                            return [
                                'id' => $item->characteristic_id,
                                'title' => $item->t('characteristic_title'),
                                'products' => $productValues // Заполняем значениями по продуктам
                            ];
                        }),
                    ];
                });
            // Отладка: выводим сгруппированные характеристики
            //dd('Grouped Characteristics:', $this->groupedCharacteristics->toArray(),$allCharacteristics);

        } catch (\Exception $e) {
            dump('Ошибка в процессе сборки характеристик:', $e->getMessage());
            dump($e->getTraceAsString());
        }
        $this->dispatch('compare-slider-updated');
        return $this->groupedCharacteristics; // Возвращаем сгруппированные характеристики

    }

    public function removeProduct(Product $product, Compare $compare){
        // Удаляем продукт из сессии сравнения
        $compare->remove($product);

        // Обновляем количество товаров по категориям
        $this->categoryCounts = $compare->countByCategory();

        // Проверяем категорию на пустоту и выполняем редирект, если текущая категория пуста
        $redirect = $this->checkAndRemoveEmptyCategory($compare);
        if ($redirect) {
            return $redirect;
        }

        // Обновляем список товаров и категорий, если текущая категория еще существует
        $this->products = $compare->getProductsByIds($compare->getCompareSession()[$this->category_id]);
        $this->categories = $compare->getExistsCategories();

        // Группируем характеристики товаров
        $this->groupCharacteristics();

        // Обновляем счетчик товаров для сравнения
        $this->dispatch('updateCompareCount')->to(CompareCount::class);
    }

    public function clearCategory($categoryId, Compare $compare)
    {
        // Получаем текущие данные из сессии сравнения
        $compareSession = $compare->getCompareSession();

        // Проверяем, существует ли категория в сессии
        if (isset($compareSession[$categoryId])) {
            // Удаляем товары данной категории
            unset($compareSession[$categoryId]);
            // Обновляем сессию
            session()->put('compare', $compareSession);
        }
        return redirect()->route('compare');
    }

    /**
     * Проверка категории на пустоту и удаление категории, если она пуста
     * Редирект, если текущая категория была удалена
     */
    public function checkAndRemoveEmptyCategory(Compare $compare)
    {
        // Проверяем, существует ли еще текущая категория
        if (!$compare->isCategoryExists($this->category_id)) {
            // Удаляем категорию из списка, если товаров больше нет
            $this->categories = $compare->getExistsCategories();

            // Выполняем редирект на страницу сравнения
            return redirect()->route('compare');
        }
    }

    public function render()
    {

        return view('livewire.compare.content',[
            'categories' => $this->categories,
            'products' => $this->products,
            'categoryCounts' => $this->categoryCounts,
            'groupedCharacteristics' => $this->groupedCharacteristics
        ]);
    }
}
