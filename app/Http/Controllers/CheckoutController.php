<?php

namespace App\Http\Controllers;

use App\Events\OrderCreate;
use App\Http\Requests\CheckPromoCodeRequest;
use App\Http\Requests\Order as OrderRequest;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryPickupPoint;
use App\Models\Order;
use App\Models\PayMethod;
use App\Models\PromoCode;
use App\Services\Analytics;
use App\Services\UnfinishedBasketService;
use App\Services\UtmLabel;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(): View
    {
        $countProducts = Cart::count();
        $payMethods = $this->preparePayMethods();
        $user = app('user') ?
            collect(app('user')->only(['email', 'phone', 'first_name', 'last_name'])) :
            collect();

        $isFbq = setting('checkbox_facebook_pixel') ?: 0;

        return view('checkout.index', compact('payMethods', 'user', 'countProducts', 'isFbq'));
    }

    public function getProducts(): Collection
    {
        return $this->prepareProducts();
    }

    public function complete(): View|RedirectResponse
    {
        $order = session('order');

        if (! $order) {
            return redirect('/');
        }

        $items = $order->cartOrderProducts;

        $analytic = new Analytics($items);

        $universalAnalytics = $analytic->universal();

        $googleAnalytics = $analytic->google();

        $dynamicAnalytics = $analytic->dynamic();

        return view('checkout.complete', compact('order', 'universalAnalytics', 'googleAnalytics', 'dynamicAnalytics'));
    }

    public function send(OrderRequest $request, UtmLabel $utmLabel): RedirectResponse
    {
        $data = $request->except(['g-recaptcha-response', '_token']);

        $order = Order::create($data)->afterSave();

        Cart::destroy();

        app(UnfinishedBasketService::class)->delete();

        $request->session()->flash('order', $order);

        OrderCreate::dispatch($order);

        if ($order->payMethod->checkout) {
            return redirect()->to($order->urlPayment());
        }

        return redirect()->route('checkout.complete');
    }

    public function checkPromoCode(CheckPromoCodeRequest $request): JsonResponse
    {
        $code = PromoCode::whereCode($request->get('code'))
            ->active()
            ->where('is_used', 0)
            ->where('date_exp', '>=', Carbon::now()->format('Y-m-d'))->first();

        if ($code) {
            if ($code->type == 'once') {
                $code->update([
                    'is_used' => 1,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'sale' => $code->sale,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __t('Промо код не найден'),
        ]);
    }

    public function searchCities(): Collection
    {
        $query = mb_convert_case(request('q'), MB_CASE_TITLE, 'UTF-8');

        $cities = City::where('title->ua', 'like', $query.'%')
            ->orWhere('title->ru', 'like', $query.'%')
            ->orWhere('title->en', 'like', $query.'%')
            ->orderBy('title->'.App::getLocale(), 'asc')->get();

        return $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'title' => $city->t('origin'),
            ];
        });
    }

    public function getDelivery(): array
    {
        $city = City::find(request('city'));

        $defaultDeliveries = Delivery::where('is_show_for_all_cities', 1)->active()->get();

        $deliveries = $city->deliveries()->active()->get()->merge($defaultDeliveries);

        $deliveriesResultDefault[] = [
            'id' => null,
            'title' => __t('Доставка'),
            'price' => 0,
            'description' => '',
            'type' => null,
        ];

        $deliveriesResult = $deliveries->sortBy('priority')->map(function ($delivery) {
            return [
                'id' => $delivery->id,
                'title' => $delivery->t('title'),
                'price' => $delivery->price,
                'description' => $delivery->t('description'),
                'type' => $delivery->type,
            ];
        })->toArray();

        return array_merge($deliveriesResultDefault, $deliveriesResult);
    }

    public function getDeliveryPickupPointers(): Collection
    {
        $points = DeliveryPickupPoint::orderBy('priority', 'asc')->get();

        return $points->map(function ($point) {
            return [
                'id' => $point->id,
                'title' => $point->t('address'),
                'text' => '',
            ];
        });
    }

    public function getDeliveryNPPointers(): Collection
    {
        $city = City::find(request('city'));

        return $this->getWarehouses(request('type'), $city);
    }

    public function getWarehouses(string $type, City $city): Collection
    {
        switch ($type) {
            case 'ukrposhta':
                $getDepartments = $city->ukrposhta();
                break;
            case 'justin':
                $getDepartments = $city->justin();
                break;
            case 'meest':
                $getDepartments = $city->meest();
                break;
            default:
                $getDepartments = $city->departments();
        }

        $departments = $getDepartments->orderBy('title->'.App::getLocale(), 'asc')->get();

        return $departments->map(function ($department) {
            return [
                'id' => $department->id,
                'title' => $department->t('title'),
            ];
        });
    }

    private function preparePayMethods(): Collection
    {
        $payMethods = PayMethod::active()->orderPriority()->get();
        $payMethodsResult[] = [
            'name' => __t('Оплата'),
            'id' => null,
        ];

        foreach ($payMethods as $payMethod) {
            $payMethodsResult[] = [
                'name' => $payMethod->t('title'),
                'id' => $payMethod->id,
            ];
        }

        return collect($payMethodsResult);
    }

    private function prepareProducts(): Collection
    {
        $productsCart = Cart::content();
        $products = [];

        foreach ($productsCart as $product) {
            $products[] = [
                'id' => $product->rowId,
                'img_link' => $product->model->getUrl(),
                'img_src' => $product->model->getImgPath(115, 115),
                'title' => $product->model->t('title'),
                'price' => $product->price,
                'count' => $product->qty,
                'total' => $product->price * $product->qty,
                'product_id' => $product->id,
                'options' => $this->getOptions($product->options),
            ];
        }

        return collect($products);
    }

    /**
     * @return array<string, string>
     */
    private function getOptions(mixed $options): array
    {
        $filters = [];

        if (count($options)) {
            foreach ($options as $characteristic => $option) {
                $characteristicModel = Characteristic::find($characteristic);
                $optionModel = CharacteristicOption::find($option);

                if ($characteristicModel && $optionModel) {
                    $filters[$characteristicModel->t('title')] = $optionModel->t('title');
                }
            }
        }

        return $filters;
    }
}
