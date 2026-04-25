<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ProductAvailableRequest;
use App\Http\Requests\ProductFieldsRequest;
use App\Http\Requests\ProductPayPartsRequest;
use App\Http\Requests\ProductPricesRequest;
use App\Http\Resources\ProductResource;
use App\Jobs\ReactivateProductsIndexByIds;
use App\Jobs\ReindexAllProducts;
use App\Jobs\ReindexUpdatedProducts;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder as RB;
use Symfony\Component\HttpFoundation\Response;

/*
GET           /products                      index   product.index
POST          /products                       store   product.store
GET           /products/{product}               show    product.show
PUT|PATCH     /products/{product}               update  product.update
DELETE        /products/{product}               destroy product.destroy
POST          /products-update                  updateList product.update.available.list
POST          /products/update/list             updateList product.update.list
*/
set_time_limit(600);
ini_set('max_execution_time', '600');

/**
 * ProductController class.
 */
class ProductController extends ApiController
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    /**
     * Список всех товаров
     * 
     * Выводит список всех товаров с пагинацией
     *
     */
    public function index(Request $request): Response
    {
        $data = Product::paginate();

        $data->getCollection()->transform(function ($product) {
            return new ProductResource($product);
        });

        return RB::success($data);
    }

    /**
     * Создание товара
     * 
     * Добавление нового товара
     *
     */
    public function store(Request $request): Response
    {
        $startModel = microtime(true);

        $data = $request->post('data');
        try {
            if (empty($data)) {
                //return response()->json(['message' => 'No data provided'], 400, $this->headers);
                return RB::asError(400)->withMessage('Не переданы данные товара')->withHttpCode(400)->build();
            }

            //TODO: создание товара

            $endModel = microtime(true);
            $timeModel = $endModel - $startModel;

            return RB::asSuccess()->withMessage('Products created, time: '.$timeModel)->withData($data)->build();

        } catch (\Exception $exception) {
            return RB::asError(400)->withMessage('Добавление товара завершилось с ошибкой: ' . $exception->getMessage())->withData($data)->build();
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    //Обновление пакета товаров по наличию

    /**
     * Статус товара, дата поступления
     *
     * Пакетное обновление товаров по полю Статус товара и Дата поступления товара.
     *
     * Передавать одним пакетом все товары, которые в наличии. Остальные товары, которых нету в пакете, будут автоматически выставлены в статус Нет в наличии и будут скрыты в каталоге.
     *
     * Передавать все поля товара. Отсутствующие поля не будут обновлены
     */

    public function updateAvailableList(ProductAvailableRequest $request)
    {
        $startModel = microtime(true);

        //$data = $request->post('data', []);
        $data = $request->toArray();

        try {
            if (empty($data)) {
                //return response()->json(['message' => 'No data provided'], 400, $this->headers);
                return RB::asError(400)->withMessage('Массив данных с товарами пуст')->withHttpCode(400)->build();
            }

            //Поскольку количество не передаем, проверку отключаем
            /*$hasQuantityGreaterThanZero = array_reduce($data, function ($carry, $item) {
                if (isset($item['quantity'])) {
                    return $carry || ($item['quantity'] > 0);
                }
                return $carry;
            }, false);*/

            Log::info('API.updateAvailableList: updating products started');

            // if ($hasQuantityGreaterThanZero) {

            $ids = $this->updateProductsFromArray($data);

            // Обновление товаров из $data
            Product::whereNotIn('id', $ids)->update([
                'product_status_id' => 6, //Не в наличии
                'arrival_date' => null, //Дата поступления
                //'has_mono_payparts' => 0,
                //'has_privat_payparts' => 0,
                //'privat_payparts_count' => 0,
                //'mono_payparts_count' => 0
            ]);

            //$this->reactivateProductsIndex($ids);
            //}
            ReindexUpdatedProducts::dispatch();


            $count = count($ids);

            Log::info('API.updateAvailableList: ' . $count . ' products updated');

            $endModel = microtime(true);
            $timeModel = $endModel - $startModel;

            return RB::asSuccess()->withMessage($count . ' products updated, time: '.$timeModel)/*->withData($data)*/->build();
            //return response()->json(['message' => $count . ' products updated'], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $exception) {
            return RB::asError(400)->withMessage('Обновление завершилось с ошибкой: ' . $exception->getMessage())->withData($data)->build();
        }
    }


    /**
     * Цены (текущая, старая)
     *
     * Пакетное обновление товаров по полю Цена и Старая цена.
     *
     * Передавать в пакете все поля. Отсутствующие поля не будут обновлены.
     *
     * Цены товаров, не переданных в пакете, не будут затронуты.
     */

    public function updatePricesList(ProductPricesRequest $request)
    {
        $startModel = microtime(true);

        $data = $request->toArray();

        try {
            if (empty($data)) {
                //return response()->json(['message' => 'No data provided'], 400, $this->headers);
                return RB::asError(400)->withMessage('Массив данных с товарами пуст')->withHttpCode(400)->build();
            }

            Log::info('API.updatePricesList: updating products started');

            $ids = $this->updateProductsFromArray($data);

            // Обновление цен товаров из $data
            //Product::whereNotIn('id', $ids)->update([
            //    'price' => 0, //Цена 0
            //    'price_old' => 0, //Цена старая 0
            //]);

            //$this->reactivateProductsIndex($ids); //реактивация в индексе отключена
            ReindexUpdatedProducts::dispatch();

            $count = count($ids);

            Log::info('API.updatePricesList: ' . $count . ' products updated');

            $endModel = microtime(true);
            $timeModel = $endModel - $startModel;

            return RB::asSuccess()->withMessage($count . ' products updated, time: '.$timeModel)/*->withData($data)*/->build();
            //return response()->json(['message' => $count . ' products updated'], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $exception) {
            return RB::asError(400)->withMessage('Обновление завершилось с ошибкой: ' . $exception->getMessage())->withData($data)->build();
        }
    }

    /**
     * Оплата частями
     *
     * Пакетное обновление товаров по полю Кол-во частей Моно и Приват рассрочка.
     *
     * Передавать одним пакетом все товары, чьи поля нужно обновить.
     *
     * Передавать все поля товара. Отсутствующие поля не будут обновлены
     */

    public function updatePayPartsList(ProductPayPartsRequest $request)
    {
        $startModel = microtime(true);

        $data = $request->toArray();

        try {
            if (empty($data)) {
                //return response()->json(['message' => 'No data provided'], 400, $this->headers);
                return RB::asError(400)->withMessage('Массив данных с товарами пуст')->withHttpCode(400)->build();
            }

            Log::info('API.updatePayPartsList: updating products started');

            $ids = $this->updateProductsFromArray($data);

            // Обновление цен товаров из $data
            Product::whereNotIn('id', $ids)->update([
                'privat_payparts_count' => 0, //Цена 0
                'mono_payparts_count' => 0, //Цена старая 0
            ]);

            ReindexUpdatedProducts::dispatch();

            $count = count($ids);

            Log::info('API.updatePayPartsList: ' . $count . ' products updated');

            $endModel = microtime(true);
            $timeModel = $endModel - $startModel;

            return RB::asSuccess()->withMessage($count . ' products updated, time: '.$timeModel)/*->withData($data)*/->build();
            //return response()->json(['message' => $count . ' products updated'], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Exception $exception) {
            return RB::asError(400)->withMessage('Обновление завершилось с ошибкой: ' . $exception->getMessage())->withData($data)->build();
        }
    }

    /**
     * Произвольные поля
     *
     * Пакетное обновление любых полей товара.
     */
    public function updateList(ProductFieldsRequest $request): JsonResponse
    {
        //$data = $request->post('data', []);
        $data = $request->toArray();

        if (empty($data)) {
            return response()->json(['message' => 'No data provided'], 400, $this->headers);
        }

        $ids = $this->updateProductsFromArray($data);
        $count = count($ids);

        ReindexUpdatedProducts::dispatch();

        Log::info('API.updateList: ' . $count . ' products updated');
        return response()->json(['message' => $count . ' products updated'], 200, $this->headers,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Получить товар
     *
     * Вывод товара по его ID
     *
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        //return new ProductResource($product);
        $data = new ProductResource($product);
        return RB::success($data);
    }

    /**
     * Retrieve other pictures for a given item.
     *
     * @param  object  $item  The item object.
     * @return \Illuminate\Support\Collection  The collection of other pictures.
     */
    private function getOtherPictures($item)
    {
        return collect(json_decode($item->other_pictures))->map(function ($picture) {
            return getUrl($picture);
        });
    }

    /**
     * Get the filters for a given item.
     *
     * @param $item The item for which to retrieve the filters.
     * @return \Illuminate\Support\Collection The collection of filters.
     */
    private function getFilters($item)
    {
        return $item->characteristics->map(function ($item) {
            return [
                'characteristic' => [
                    'id' => $item->characteristic->id,
                    'name' => json_decode($item->characteristic->title),
                ],
                'value' => [
                    'id' => $item->characteristicOption->id,
                    'name' => json_decode($item->characteristicOption->title),
                ],
            ];
        });
    }

    /**
     * Checks if the number of updated products is greater than zero and triggers a reindexing process if needed.
     *
     * @param int $updatedProductsCount The number of updated products.
     * @return void
     */
    /*private function reindexProductsIfNeeded(int $updatedProductsCount): void
    {
        if ($updatedProductsCount > 0) {
            //Cache::tags(['promotion_filter', 'tree', 'category', 'products', 'elasticsearch', 'category_filter'])->flush();
            //Artisan::call('cache:clear_rebuild');
            ReindexAllProducts::dispatch();
        }
    }*/

    /**
     * Reactivates the products index for the specified product IDs.
     *
     * @param array $productIds An array of product IDs to reactivate the index for.
     * @return void
     */
    /*private function reactivateProductsIndex(array $productIds = []): void
    {
        if ($productIds) {
            ReactivateProductsIndexByIds::dispatch($productIds);
        }
    }*/

    /**
     * Update products from an array of data.
     *
     * @param array $data The array of data containing the products to be updated.
     * @return array The array of updated product IDs.
     */
    private function updateProductsFromArray(array $data): array
    {
        $updatedIds = [];

        // Разбиваем данные на чанки
        collect($data)->chunk(100)->each(function ($chunk) use (&$updatedIds) {
            $preparedData = [];

            // Подготавливаем данные для текущего чанка
            foreach ($chunk as $value) {
                $id = (int) $value['id'];
                $value['id'] = $id; // Удаляем ID из массива данных для обновления

                if(isset($value['price'])) {
                    $value['price'] = $this->preparePrice($value['price']);
                }
                if(isset($value['price_old'])) {
                    $value['price_old'] = $this->preparePrice($value['price_old']);
                }

                $preparedData[] = $value;
            }

            // Массово обновляем записи с использованием Laravel Mass Update
            Product::massUpdate(
                values: $preparedData,
                uniqueBy: 'id'
            );

            // Записываем обновленные ID
            foreach ($preparedData as $item) {
                $updatedIds[] = $item['id'];
            }
        });

        return $updatedIds;
    }

    //Переиндексирование измененных товаров
    public function reindexUpdatedProducts()
    {
        try {
            ReindexUpdatedProducts::dispatch();
            return RB::asSuccess()->withMessage('ProductController: начат фоновый процесс переиндексации измененных товаров')->build();
        } catch (\Exception $exception) {
            return RB::asError()->withMessage('ProductController: ' . $exception->getMessage())->build();
        }
    }

    //Полное переиндексирование товаров с пересозданием индекса
    public function reindex()
    {
        try {
            ReindexAllProducts::dispatch();
            return RB::asSuccess()->withMessage('ProductController: начат фоновый процесс переиндексации всех товаров')->build();
        } catch (\Exception $exception) {
            return RB::asError()->withMessage('ProductController: ' . $exception->getMessage())->build();
        }
    }

    /**
     * Преобразование числа с запятой в число с точкой.
     *
     * @param string $number
     * @return string
     */
    private function preparePrice($number)
    {
        // Преобразуем запятую в точку
        return str_replace(',', '.', $number);
    }
}
