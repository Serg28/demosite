<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteNovaPoshtaRequest;
use App\Http\Requests\NovaPoshtaRequest;
use App\Models\City;
use App\Models\NPWarehouse;
use App\Models\Order;
use App\Services\NovaPoshtaApi2;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NovaPoshtaController extends Controller
{
    private $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    private $np;

    public function __construct(NovaPoshtaApi2 $np)
    {
        $this->np = $np;
    }

    //Вывод формы создания/редактирования ТТН
    /*public function form(Order $order): JsonResponse
    {
        //$np_info = json_decode($order->np_info, true);
        $np_info = $order->np_info;
        $warehouses = City::where('ref',setting('client_np_cityref'))->with(['departments'])->get();

        $departments = $warehouses->flatMap(function ($warehouse) {
            return $warehouse->departments;
        });


        $form = view('cms.modals.form_np_en', compact('order', 'np_info', 'departments'))->render();

        return response()->json(['html' => $form, 'status' => true], 200, $this->headers,
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }*/

    public function getCities()
    {
        // Получаем параметр поиска
        $search = request()->get('q', '');
        $current = request()->get('currentVal', '');
        $limit = request()->get('limit', 20);

        // Используем get(), чтобы получить коллекцию, а не одну запись
        $cities = City::select(['ref', 'title'])
            ->when($search, function ($query, $search) {
                // Поиск по полю title, если параметр q присутствует
                $query->where('title->ru', 'like', '%' . strtolower($search) . '%')
                ->orWhere('title->ua', 'like', '%' . strtolower($search) . '%');
            })
            ->limit($limit)
            ->get(); // Получаем коллекцию вместо одного объекта

        // Применяем map к коллекции
        $result = $cities->map(function ($city) use ($current) {
            return [
                'id' => $city->ref,
                'text' => $city->t('title'), // Применяем метод t() к заголовку
                'selected' => $city->ref == $current
            ];
        })->toArray();

        return json_encode($result);
    }


    public function getDepartments()
    {
        // Получаем параметр поиска
        $search = request()->get('q', '');
        $current = request()->get('currentVal', '');
        $limit = request()->get('limit', 20);
        $client_np_cityref = request()->get('city', setting('client_np_cityref'));

        //$city = City::where('ref', setting('client_np_cityref'))
        $city = City::where('ref', $client_np_cityref)
            ->with(['departments' => function ($query) use ($search, $limit) {
                $query->select('ref', 'title', 'city_id', 'num')
                    ->when($search, function ($query, $search) {
                        $query->where('num', 'Like', $search . '%');
                        if ($query->count() == 0) {
                            $query->orWhere('title->ru', 'like', '%' . strtolower($search) . '%')
                                ->orWhere('title->ua', 'like', '%' . strtolower($search) . '%');
                        }
                    })->limit($limit);
            }])
            ->first();

        $departments = $city->departments->map(function ($department) use ($current) {
            return [
                'id' => $department->ref,
                'text' => $department->t('title'), // Применяем метод t() к заголовку
                'selected' => $department->ref == $current
            ];
        })->toArray();

        return json_encode($departments);

        return response()->json(['results' => $departments, 'status' => true], 200, $this->headers);
    }


    public function form(Order $order): JsonResponse
    {
        $npInfo = $order->np_info;
        $np_info = $npInfo;

        //Это вариант с загрузкой всех данных складов отправителя прямо в шаблон - долго
        /*$departments = Cache::remember('departments', 3600, function () {
            return City::where('ref', setting('client_np_cityref'))->with('departments:ref,title')->get()->flatMap->departments;
        });*/

        /*$departments = City::where('ref', setting('client_np_cityref'))
            ->with(['departments' => function ($query) {
                $query->select('ref', 'title', 'city_id');
            }])
            ->cursor()
            ->flatMap->departments;*/

        //Будем в шаблоне загружать склады черех ajax - методом getDepartments()
        //Только сначала получаем данные о складе отправителя для первоначальной установки значений в форме
        $client_np_ref = isset($np_info['np_client_np_ref']) && !empty($np_info['np_client_np_ref']) ? $np_info['np_client_np_ref'] : setting('client_np_ref');

        $np_info['np_client_np_cityref'] = (isset($np_info['np_client_np_cityref']) && !empty($np_info['np_client_np_cityref'])) ? $np_info['np_client_np_cityref'] : setting('client_np_cityref');

        $senderNPWarehouse = NPWarehouse::where('ref', $client_np_ref)
            ->first(['ref', 'title']);

        $senderNPCity = City::where('ref', $np_info['np_client_np_cityref'])
            ->first(['ref', 'title']);

        $senderNPWarehouse = $senderNPWarehouse ? [
            'ref' => $senderNPWarehouse->ref,
            'text' => $senderNPWarehouse->t('title')
        ] : null;

        $senderNPCity = $senderNPCity ? [
            'ref' => $senderNPCity->ref,
            'text' => $senderNPCity->t('title')
        ] : null;

        //Сброc контактных полей, если накладная удалена или не создана (трек-номер отстутствует).
        //Например, накладная была создана, но потом ее удалили и создали новую. В таком случае нужно подтягивать контактные данные не из прошлой ТТН,
        //а из заказа. Для этого поля из старой ТТН обнуляем и далее в шаблоне сработает проверка: если поле пустое, берем данные из заказа
        //При удалении накладной данные о ней удаляются, но все же для подстраховки принудительно почистим поля
        if (empty($order->tracking_num) && is_array($np_info)) {
            $resetFields = ['np_order_id', 'firstname', 'lastname', 'phone', 'city', 'warehouse', 'np_client_np_ref', 'np_client_np_cityref'];
            foreach ($resetFields as $field) {
                if (array_key_exists($field, $np_info)) {
                    unset($np_info[$field]);
                }
            }
        }

        $form = view('cms.modals.form_np_en', compact('order', 'npInfo', 'np_info', /*'departments'*/ 'senderNPWarehouse', 'senderNPCity'))->render();

        return response()->json(['html' => $form, 'status' => true], 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //Обновление ТТН
    public function update(NovaPoshtaRequest $request)
    {
        $data = $this->ttn($request, 'updateDocument');

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //Создание новой ТТН
    public function store(NovaPoshtaRequest $request)
    {
        $data = $this->ttn($request, 'newInternetDocument');

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //удаляем накладную с НП и БД
    public function delete(DeleteNovaPoshtaRequest $request)
    {
        //Ищем ref ЕН в поле np_info
        $order_info = Order::where('id', $request->post('orderid'))->value('np_info');
        //$order_info = json_decode($order_info, true);
        $ref = $order_info['ref'];

        if (! empty($ref)) {
            $result = $this->np
                ->model('InternetDocument')
                ->method('delete')
                ->params([
                    'DocumentRefs' => $ref,
                ])
                ->execute();

            $data = [];
            if ($result['success'] == 1 and empty($result['error'])) {
                $fields = ['np_info' => [], 'tracking_num' => ''];

                if (Order::find($request->post('orderid'))->update($fields)) {
                    $data['status'] = 'ok';
                    $data['success'] = true;
                    $data['type'] = 'delete';
                    $data['message'] = __cms('Удаление ЕН успешно выполнено');
                } else {
                    $data['success'] = false;
                    $data['error'] = __cms('Ошибка при записи заказа в базу данных или заказ не существует');
                }
            } else {
                $data['success'] = false;
                $data['error'] = __cms('Ошибка удаление ЕН на стороне Новой Почты');
            }
        } else {
            $data['success'] = false;
            $data['error'] = __cms('Ошибка при удалении ЕН: для заказа не было создано ЕН, ошибка при поиске заказа в БД сайта или заказ не существует');
        }

        return response()->json($data, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function ttn(NovaPoshtaRequest $request, $method = 'newInternetDocument')
    {
        //$np_info = json_encode($request->post());
        $np_info = $request->post();
        $post = $request->post();
        $city_area = '';
        $order_info_ref = '';

        $order = Order::where('id', $request->post('orderid'));

        if ($method == 'newInternetDocument') {
            $fields = ['np_info' => $np_info];
            $order->update($fields);
        } else {
            $order_info = $order->value('np_info');
            //$order_info = json_decode($order_info, true);

            $order_info_ref = $order_info['ref'];
        }

        if ($order) {
            $order = $order->first();
            // Перед генерированием ЭН необходимо получить данные отправителя
            // Получение всех отправителей
            $senderInfo = $this->np->getCounterparties('Sender', 1, '', '');

            // Выбор отправителя в конкретном городе (в данном случае - в первом попавшемся)
            $sender = $senderInfo['data'][0]; //$senderInfo['data'][1];

            //получаем контактные данные контрагента
            $ContactSender = $this->np->getCounterpartyContactPersons($sender['Ref']);

            $paymentMethod = 'Cash';

            switch ($request->post('np_delivery_pay')) {
                case 'Одержувач':
                    $np_delivery_pay = 'Recipient';
                    $paymentMethod = 'Cash';
                    break;

                case 'Відправник':
                    $np_delivery_pay = 'Sender';
                    $paymentMethod = 'NonCash';
                    break;
            }

            //формируем массив с перечнем мест, берем даныне из массива np_places_array
            if (! empty($request->post('np_places_array'))) {
                $np_places_array = [];
                $i = 0;
                foreach ($request->post('np_places_array') as $value) {
                    //$np_places_array[$i]['volumetricVolume'] = ;
                    $np_places_array[$i]['volumetricWidth'] = $value[0]['np_places_width'];
                    $np_places_array[$i]['volumetricLength'] = $value[0]['np_places_length'];
                    $np_places_array[$i]['volumetricHeight'] = $value[0]['np_places_height'];
                    $np_places_array[$i]['weight'] = $value[0]['np_places_weight'];
                    $i++;
                }
            }

            if (! empty($request->post('city'))) {
                //получаем область
                /*
                $city_area = $this->np->getCity(trim($_POST['city']));
                $city_area = $this->np->getAreas($city_area['data'][0]['Area']);
                $city_area = $city_area['data'][0]['Description'];
                */
                $get_city = $this->np->getCity(trim($request->post('city')));
                $get_area = $this->np->getAreas($get_city['data'][0]['Area']);
                $get_area_name = $get_area['data'][0]['Description'];
            }

            //создаем новую накладную //$this->np->newInternetDocument
            $arr3 = [
                'Ref' => $order_info_ref,
                // Дата отправления
                'DateTime' => date('d.m.Y'),
                // Тип доставки, дополнительно - getServiceTypes()
                'ServiceType' => 'WarehouseWarehouse',
                //https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b6483890e
                // Тип оплаты, дополнительно - getPaymentForms()

                'InfoRegClientBarcodes' => $request->post('np_order_id'),

                'PaymentMethod' => $paymentMethod,
                // Форма розрахунку Cash/NonCash string[36]
                // Кто оплачивает за доставку
                'PayerType' => $np_delivery_pay,
                // Стоимость груза в грн
                'Cost' => $request->post('np_price'),
                // Кол-во мест
                'SeatsAmount' => $request->post('np_places'),
                'OptionsSeat' => $np_places_array,

                // Описание груза
                'Description' => $request->post('np_comment'),
                // Тип доставки, дополнительно - getCargoTypes
                'CargoType' => 'Cargo',
                // Вес груза
                'Weight' => $request->post('np_weight_general_kg'),
                // Объем груза в куб.м.
                //'VolumeGeneral' => '0.5',
                'VolumeGeneral' => $request->post('np_volume_general_m3'),
                // Обратная доставка
                //'BackwardDeliveryData' => ''
            ];

            //Сумма наложенного платежа, если он задан
            if (!empty($request->post('np_after_payment_cost'))) {
                $arr3['AfterpaymentOnGoodsCost'] = (float)$request->post('np_after_payment_cost');
            }

            if ($method == 'newInternetDocument') {
                unset($arr3['Ref']);
            }

            try {
                $result = $this->np->$method(
                    // Данные отправителя
                    [
                        // Данные пользователя
                        'Sender' => $sender['Ref'],
                        //'CitySender' => setting('client_np_cityref'),
                        'CitySender' => ($request->post('np_client_np_cityref')) ? $request->post('np_client_np_cityref') : setting('client_np_cityref'),
                        //Киев = 8d5a980d-391c-11dd-90d9-001a92567626; у приватної особи немає міста, вона може відправляти звідусіль $sender['City'],
                        'ContactSender' => $ContactSender['data'][0]['Ref'],
                        'SendersPhone' => $ContactSender['data'][0]['Phones'],
                        'SenderAddress' => ($request->post('np_client_np_ref')) ? $request->post('np_client_np_ref') : setting('client_np_ref'),
                        //$senderWarehouses['data'][159]['Ref'] адрес склада отправителя выбирается вручную из массива Відділення №203

                    ],
                    // Данные получателя получаем из POST
                    [
                        //'FirstName' => trim($request->post('firstname')),
                        'FirstName' => str_replace( ['ʼ'], ["'"], preg_replace('/[^\pL\s]/u', '', trim($request->post('firstname'))) ),
                        'MiddleName' => '',
                        //'LastName' => trim($request->post('lastname')),
                        'LastName' => str_replace( ['ʼ'], ["'"], preg_replace('/[^\pL\s]/u', '', trim($request->post('lastname'))) ),
                        'Phone' => str_replace(
                            ['(', ')', '-', ' '],
                            ['', '', '', ''],
                            $request->post('phone')
                        ),
                        //'City' => $request->post('city'),
                        'City' => str_replace( ['ʼ'], ["'"], trim($request->post('city')) ),
                        'CityRef' => $get_city['data'][0]['Ref'],
                        'Region' => $city_area, //$get_area['data'][0]['Ref']
                        'Warehouse' => $request->post('warehouse'),
                        'WarehouseRef' => $order->npWarehouse->ref,
                        /*'City' => 'Київ',
                            'Region' => 'Київська',
                            'Warehouse' => 'Відділення №3: вул. Калачівська, 13 (Стара Дарниця)',*/

                    ],
                    $arr3
                );
            } catch (\Exception $e) {
                //dd($order->npWarehouse->ref);
                $data['success'] = false;
                $data['error'] = __cms('Что-то пошло не так. Вероятно, некоторые указанные данные не прошли проверку на стороне Новой Почты. Ответ Новой Почты: '). $e->getMessage();
                Log::info('Ошибка создания ТТН НП, заказ ' . $order->id. ': '. $e->getMessage());
                Log::info('Ошибка создания ТТН НП, заказ ' . $order->id. ': '. print_r($arr3,1));
                return $data;
            }

            $data = [];
            if (@$result['success'] == 1 and @empty($result['error'])) {
                //добавляем
                $data['ref'] = $result['data'][0]['Ref'];
                $data['CostOnSite'] = $result['data'][0]['CostOnSite'];
                $data['IntDocNumber'] = $result['data'][0]['IntDocNumber'];

                $post['ref'] = $data['ref'];
                $post['CostOnSite'] = $data['CostOnSite'];
                $post['IntDocNumber'] = $data['IntDocNumber'];

                //$update_np_info = json_encode($post);
                $update_np_info = $post;

                $fields = ['np_info' => $update_np_info, 'tracking_num' => $data['IntDocNumber']];
                if ($method == 'newInternetDocument') {
                    $fields['order_status_id'] = 3;
                }

                if (Order::find($request->post('orderid'))->update($fields)) {
                    $data['status'] = 'ok';
                    $data['success'] = true;
                    $data['type'] = ($method == 'newInternetDocument') ? 'created' : 'updated';
                    $data['message'] = str_replace(
                        ['[ttn]', 'price'],
                        [$post['IntDocNumber'], $post['CostOnSite']],
                        __cms('Все хорошо! ЭН создана: трекинг номер – [ttn], стоимость доставки – [price] грн. Не забудьте сохранить заказ!')
                    );
                } else {
                    $data['success'] = false;
                    $data['error'] = __cms('Ошибка при записи заказа в базу данных или заказ не существует');
                }
            } else {
                if ($result['errors'][0] == 'Recipient not selected') {
                    array_unshift($result['errors'], __cms('Имя или фамилия написано с использованием английских букв.'));
                }
                $data['error'] = __cms('Ошибка ЭН').': '.implode('; ', $result['errors']);
            }
        } else {
            $data['success'] = false;
            $data['error'] = __cms('Ошибка при записи заказа в базу данных или заказ не существует');
        }

        return $data;
    }

    public function tracking(Order $order)
    {
        //$np_info = json_decode($order->np_info, true);
        $np_info = $order->np_info;
        $response = [
            'success' => false,
        ];
        if ($order->tracking_num && $np_info['phone']) {
            $response = $this->np->documentsTracking($order->tracking_num, $np_info['phone']);
            $response = [
                'success' => $response['success'],
                'data' => $response['data'][0],
            ];
        }

        return response()->json($response, 200, $this->headers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    //public function print($ref) {
    public function print(Order $order, $type = 'pdf')
    {
        //$np_info = json_decode($order->np_info, true);
        $np_info = $order->np_info;
        if ($np_info['ref']) {
            switch ($type) {
                case 'pdf':
                    $type = 'pdf_link';
                    break;
                default:
                    $type = 'html_link';
                    break;
            }
            $response = $this->np->printDocument($np_info['ref'], $type);
            if ($response['success'] == true) {
                return redirect()->away($response['data'][0]);
            }
        }
    }
}
