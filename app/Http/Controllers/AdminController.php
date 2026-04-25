<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\News;
use App\Models\Order;
use App\Models\OrderReceipt;
use App\Services\CheckboxUa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Vis\Builder\Libs\GoogleTranslateForFree;
use Vis\Builder\Models\Language;

class AdminController extends Controller
{
    public function getCharacteristic(Characteristic $characteristic): View
    {
        $options = $characteristic->options;

        return view('cms.fields.options_for_characteristic', compact('options'))->render();
    }

    public function getTreeForMenu()//: View
    {
        $type = request('type');
        $data = [];

        if ($type !== "App\Models\MenuSection" && $type !== "App\Models\MenuColSection" && $type!=='0') {
            $model = new $type();
        } else {
            return '';
        }

        if (is_subclass_of($model, 'App\Models\BaseModel')) {

            $modelData = $model::orderBy('created_at', 'desc')->get();

            foreach ($modelData as $item) {
                $data[$item->id] = $item->t('title');
            }

        } else {

            $nodes = $model::where('id', '!=', 1)->defaultOrder()->get()->toTree();
            $traverse = function ($categories, $prefix = ' - ') use (&$traverse, &$data): void {
                foreach ($categories as $category) {
                    $data[$category->id] = $prefix.' '.$category->t('title');
                    $traverse($category->children, $prefix.$category->t('title').' / ');
                }
            };

            $traverse($nodes);
        }

        return view('cms.fields.select_for_tree', compact('data'))->render();
    }

    public function setManager(Order $order): RedirectResponse
    {
        $order->manager_id = app('user')->id;
        $order->save();
        return redirect()->to('/admin/orderedit?o='.$order->id);
        //return back();
    }

    public function createNewOption(): JsonResponse
    {
        if (request('characteristic_id')) {
            $languages = (new Language())->getLanguages()->pluck('language');
            $title = [];

            foreach ($languages as $language) {
                $title[$language] = (new GoogleTranslateForFree())->translate(
                    defaultLanguage(),
                    $language,
                    request('option'),
                    2
                );
            }

            $option = CharacteristicOption::create([
                'characteristic_id' => request('characteristic_id'),
                'title' => json_encode($title),
                'is_active' => 1,
                'slug' => Str::slug(request('option')),
            ]);

            return response()->json([
                'id' => $option->id,
                'name' => request('option'),
            ]);
        }
    }


    /**
     * @param int $id - ID заказа
     * @param string $type - тип чека: 'prepayment' - предоплата, 'main_payment' - полный платеж (вся сумма или остаток после предоплаты);
     * @return JsonResponse|null
     */
    public function sendToCheckbox(int $id, $type = 'main_payment'): ?JsonResponse
    {
        $order = Order::find($id);

        //if (! $order->orderReceipt) {
        if (!$order->orderReceipt->where('type', '=', $type)->first()) {

            $checkbox = new CheckboxUa($order->recipient, $type);
            $response = $checkbox->createReceipt($order);

            if(!$response) {
                return response()->json(['message' =>  __cms('Не удалось получить ответ от сервиса Checkbox.ua. Возможно, технические проблемы. Попробуйте немного позже')]);
            }

            Log::info('sendToCheckbox order '.$order->id.': '.print_r($response, 1).', recipient: '.print_r(($order->recipient->toArray() ?? []), 1));
            if (is_array($response) && array_key_exists('message', $response)) {
                return response()->json(['message' => $response['message']/*.'. '.__cms('Проверьте, чтобы количество и суммы не были нулевыми').'. '.__cms('Затем повторите фискализацию снова')*/]);
            }
            $receipt = OrderReceipt::create([
                'uuid' => $response['id'],
                'order_id' => $order->id,
                'type' => $type
            ]);
            if ($order->email) {
                $checkbox->sendReceiptToEmail($receipt, $order);
            }
            //$order->update(['is_online_payed'=>1]);

            return response()->json(['receipt' => $receipt]);
        }

        return null;
    }

    public function xmlFeedLoadOption(): string
    {
        $characteristicOptions = CharacteristicOption::where('characteristic_id', request()->get('characteristic_id'))->get();
        $field = request()->get('field');

        return view('cms.fields.many_to_many_options_xml_ajax', compact('characteristicOptions', 'field'))->render();
    }

    public function loadOrderLogs(Order $order)
    {
        $logs = $order->revisionHistory()
            ->leftJoin('users', 'revisions.user_id', '=', 'users.id')
            ->whereNotNull('revisions.new_value')
            //->where('revisions.new_value', '<>', '')
            //->where('revisions.old_value', '<>', 0)
            //->where('revisions.key', '<>', 'np_info')
            ->select('revisions.*', 'users.first_name', 'users.last_name')
            ->latest('revisions.created_at')
            ->get();

        return view('cms.fields.logs_for_orders_ajax', compact('logs'))->render();
    }

    public function changeSideBar(string $view = ''): void
    {
        Cookie::queue('tb-misc-body_class', $view, 5000);
    }
}
