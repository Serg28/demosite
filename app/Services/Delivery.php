<?php

namespace App\Services;

use App\Models\City;
use App\Models\Delivery as DeliveryModel;
use App\Models\DeliveryPickupPoint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class Delivery
{

    public function get()
    {
        return session()->get('delivery');
    }

    /*public function getPrice($subtotal = 0)
    {
        if ($delivery = $this->get()) {
            return (($subtotal >= $delivery->free_cost) && !is_null($delivery->free_cost)) ? 'free' : $delivery->price;
        }

        return 0;
    }


    //Выводит текстовое представление стоимости доставки в чекауте
    public function getPriceDescription(): ?string
    {
        $delivery = $this->get();

        if ($this->getPrice() === 'free') {
            return __t('бесплатно');
        }

        if ($delivery) {
            return $this->getPrice() ?: strip_tags($this->get()->t('description'));
        }

        return null;
    }*/

    public function getPrice($subtotal = 0)
    {
        if ($delivery = $this->get()) {
            // Проверяем, что free_cost не NULL и не пустое значение, но 0 разрешено

            if (($delivery->free_cost !== null && $delivery->free_cost !== '' && $delivery->free_cost>0 && $subtotal >= $delivery->free_cost) || $delivery->price === 0) {
                return 'free';
            }

            return $delivery->price;
        }

        return 0;
    }

    //Выводит текстовое представление стоимости доставки в чекауте
    public function getPriceDescription($subtotal = 0): ?string
    {
        $delivery = $this->get();

        if ($this->getPrice($subtotal) === 'free') {
            return __t('бесплатно');
        }

        if ($delivery) {
            return $this->getPrice() ?: strip_tags(__t('За тарифом перевізника'));
            //return $this->getPrice() ?: 0;
        }

        return null;
    }


    public function setDelivery($delivery_id = null): void
    {
        if (!$delivery_id) {
            $this->resetDelivery();
            return;
        }

        //получение данных о доставке
        $delivery = DeliveryModel::where('id', '=', $delivery_id)->active()->first();

        session()->put('delivery', $delivery);
    }

    public function resetDelivery(): mixed
    {
        return session()->remove('delivery');
    }

    public function removeDelivery()
    {
        return $this->resetDelivery();
    }

    /**
     * Получение списка доставок для указанного города.
     *
     * @param int|string|null $cityId Идентификатор города.
     *
     * @return \Illuminate\Support\Collection Список доставок.
     */
    public function getDeliveries(int|string $cityId = null, bool $ignoreCity = false): Collection
    {

        $deliveries = (!$ignoreCity) ? $this->getDeliveriesByCity($cityId) : $this->getAllDeliveries();

        //$deliveryTerm = $this->getDeliveryTerm(); //TODO: настроить сроки доставки
        $deliveryTerm = [];

        return $deliveries->sortBy('priority')->map(function ($delivery) use ($deliveryTerm) {
            return [
                'id'          => $delivery->id,
                'title'       => $delivery->t('title'),
                'price'       => $delivery->price,
                'description' => $delivery->t('description'),
                'type'        => $delivery->type,
                'term'        => $deliveryTerm[$delivery->id]['description'] ?? '',
            ];
        });
    }

    /**
     * Получение списка видов доставки для указанного города.
     *
     * @param int|string|null $cityId Идентификатор города.
     *
     * @return Collection Список видов доставки.
     */
    public function getDeliveriesByCity(int|string $cityId = null): Collection
    {
        if ($cityId) {
            $city = City::find($cityId);

            if ($city) {
                $defaultDeliveries = DeliveryModel::where('is_show_for_all_cities', 1)
                    ->active()
                    ->orderBy('priority')
                    ->get();

                return $city->deliveries()
                    ->active()
                    ->get()
                    ->merge($defaultDeliveries);
            }
        }

        return collect();
    }

    //Получение списка всех методов доставки
    public function getAllDeliveries()
    {
        return DeliveryModel::active()->orderBy('priority')->get();
    }

    //Получение списка пунктов самовывоза
    public function getDeliveryPickupPointers(): Collection
    {
        return DeliveryPickupPoint::orderBy('priority', 'asc')->get()->map(function ($point) {
            return [
                'id' => $point->id,
                'num' => $point->num,
                'title' => $point->t('address'),
                'text' => '',
            ];
        });
    }

    //Получаем список отделений или точек самовывоза для города
    public function getDeliveryWarehousesPointers(int|string $cityId = null, string $delivery_type = null): Collection
    {
        $city = City::find($cityId);

        return $city ? $this->getWarehouses($city, $delivery_type ) : collect([]);
    }

    //Получаем список отделений Новой почты (отделения) для города
    /*public function getDeliveryNPPointers(int|string $cityId = null): Collection
    {
        return $this->getDeliveryWarehousesPointers($cityId, 'np');
    }*/

    //Вывод формы с полями для выбранного метода доставки (для HTML варианта, когда список складов и точек вывоза загружается сразу в шаблон для работы с foreach в blade)
    public function getHtmlDeliveryPointers(int|string $city_id = null, int|string $delivery_id = null, string $delivery_type = null, mixed $checkoutErrors = null): ?string
    {
        //Устанавливаем данные о выбранной доставке. Для пересчета цены доставки в чекауте
        $this->setDelivery($delivery_id);

        //Если тип (алиас) доставки не указан, берем его из БД
        if(!$delivery_type) {
            $delivery_type = DeliveryModel::find($delivery_id)->type ?? '';
        }

        $points = match ($delivery_type) {
            'pickup' => $this->getDeliveryPickupPointers(),
            default => $this->getDeliveryWarehousesPointers($city_id, $delivery_type),
        };

        $results = $points->map(function ($point) {
            return [
                'id' => $point['id'],
                'title' => $point['title'],
            ];
        });

        return ($delivery_id) ? view('livewire.checkout.forms.delivery_' . $delivery_id, compact('points','city_id', 'delivery_id', 'delivery_type', 'checkoutErrors' )
        )->render() : __t('Оберіть один зі способів доставки');
    }

    //Вывод формы с полями для выбранного метода доставки - для работы с ajax/livewire. Без загруженных складов, только форма.
    // Все данные о складах нужно дополнительно подгружать аяксом уже внутри сформированного шаблона
    public function getDeliveryPointers(int|string $city_id = null, int|string $delivery_id = null, string $delivery_type = null, mixed $checkoutErrors = null): ?string
    {
        //Устанавливаем данные о выбранной доставке. Для пересчета цены доставки в чекауте
        $this->setDelivery($delivery_id);

        //Если тип (алиас) доставки не указан, берем его из БД
        if(!$delivery_type) {
            $delivery_type = DeliveryModel::find($delivery_id)->type ?? '';
        }

        return ($delivery_id) ? view('livewire.checkout.forms.delivery_' . $delivery_id, compact('city_id', 'delivery_id', 'delivery_type', 'checkoutErrors' )
        )->render() : __t('Оберіть один зі способів доставки');
    }

    /**
     * Получает склады или пункты выдачи по типу доставки для указанного города.
     *
     * Метод динамически вызывает соответствующий метод модели `City` на основе переданного типа доставки.
     * Возвращает связь HasMany складов или пунктов выдачи для указанного города и типа доставки.
     *
     * @param int|City $city Экземпляр города или его ID для получения складов или пунктов выдачи.
     * @param string $type Тип доставки. Поддерживаемые типы:
     *                     - 'ukrposhta'
     *                     - 'justin'
     *                     - 'meest'
     *                     - 'rozetka'
     *                     - 'pickup'
     *                     - 'np_pochtomat'
     *                     - 'np' (по умолчанию)
     * @return mixed
     */
    public function getWarehousesByCityDeliveryType(int|City $city, string $type): mixed
    {
        return once(static function () use ($city, $type) {
            // Проверка, является ли $city числом
            if (is_numeric($city)) {
                // Попробуем найти город по ID
                $city = City::find((int) $city); // Преобразуем к числу и ищем город
            }

            // Проверка, является ли $city объектом модели City
            if (!$city instanceof City) {
                //throw new \InvalidArgumentException('Параметр city должен быть объектом City или числовым ID.');
                return null;
            }

            $methods = [
                'ukrposhta' => 'ukrposhta',
                'justin' => 'justin',
                'meest' => 'meest',
                'rozetka' => 'rozetka',
                'pickup' => 'pickup',
                'np_pochtomat' => 'novaposhtaPochtomat',
                'np' => 'novaposhtaWarehouse',
            ];

            // Получаем название метода из массива по ключу $type, если его нет, используем 'novaposhtaWarehouse'
            $method = $methods[$type] ?? 'novaposhtaWarehouse';

            // Вызываем метод динамически
            return $city->$method();
        });
    }

    /**
     * Получает улицы по типу доставки для указанного города.
     *
     * Метод динамически вызывает соответствующий метод модели `City` на основе переданного типа доставки.
     * Возвращает связь HasMany улиц для указанного города и типа доставки.
     *
     * @param int|City $city Экземпляр города или его ID для получения улиц.
     * @param string $type Тип доставки. Поддерживаемые типы:
     *                     - 'np' (по умолчанию)
     * @return mixed
     */
    public function getStreetsByCityDeliveryType(int|City $city, string $type): mixed
    {
        return once(static function () use ($city, $type) {
            // Проверка, является ли $city числом
            if (is_numeric($city)) {
                // Попробуем найти город по ID
                $city = City::find((int) $city); // Преобразуем к числу и ищем город
            }

            // Проверка, является ли $city объектом модели City
            if (!$city instanceof City) {
                //throw new \InvalidArgumentException('Параметр city должен быть объектом City или числовым ID.');
                return null;
            }

            //Можно добавить другие методы доставки и соответствующий ей метод со связью HasMany в модели City
            $methods = [
                'np' => 'novaposhtaStreet',
            ];

            // Получаем название метода из массива по ключу $type, если его нет, используем 'novaposhtaWarehouse'
            $method = $methods[$type] ?? 'novaposhtaStreet';

            // Вызываем метод динамически
            return $city->$method();
        });
    }

    //Поиск отделений по поисковому запросу в рамках указанного метода доставки (его алиаса) + город
    public function getWarehouses(City $city, string $type = null, string $searchTerm = null): Collection
    {
        $getDepartments = $this->getWarehousesByCityDeliveryType($city, $type);

        $departments = $getDepartments
            ->where(function ($query) use ($searchTerm, $getDepartments) {
                $query->when(Schema::hasColumn(
                        $getDepartments->getRelated()->getTable(),
                        'postcode'
                    ) && filled($searchTerm), function ($query) use ($searchTerm) {
                    $query->where('postcode', 'Like', '%' . $searchTerm . '%');
                    if ($query->count() === 0) {
                        $query->orWhere('title->' . App::getLocale(), 'like', '%' . $searchTerm . '%');
                    }
                })
                    ->when(
                        Schema::hasColumn($getDepartments->getRelated()->getTable(), 'num') && filled($searchTerm),
                        function ($query) use ($searchTerm) {
                            $query->where('num', 'Like', $searchTerm . '%');
                            if ($query->count() === 0) {
                                $query->orWhere('title->' . App::getLocale(), 'like', '%' . $searchTerm . '%');
                            }
                        }
                    );
            })->orderByRaw('ISNULL(num), num ASC')->orderBy(
                'title->' . App::getLocale(),
                'asc'
            )->get();

        return $departments->map(function ($department) {
            return [
                'id' => $department->id,
                'title' => $department->t('title'),
            ];
        });
    }

    //Поиск улиц по поисковому запросу в рамках указанного метода доставки (его алиаса) + город
    public function getStreets(City $city, string $type = null, string $searchTerm = null): Collection
    {
        $getDepartments = $this->getStreetsByCityDeliveryType($city, $type);

        $departments = $getDepartments
            ->where(function ($query) use ($searchTerm) {
                $query->when(filled($searchTerm),
                    function ($query) use ($searchTerm) {
                        $query->where('title->' . App::getLocale(), 'like', '%' . $searchTerm . '%');
                    }
                );
            })->orderBy(
                'title->' . App::getLocale(),
                'asc'
            )->get();

        return $departments->map(function ($department) {
            return [
                'id' => $department->ref,
                'title' => $department->t('title'),
            ];
        });
    }
}
