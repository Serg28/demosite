<?php

namespace App\Livewire\Product;

use App\Events\QuickOrderCreate;
use App\Helpers\PhoneNumberHelper;
use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\Product;
use App\Services\UtmLabel;
use App\Traits\LivewireRecaptchable;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class QuickOrder extends Component
{
    use LivewireRecaptchable;

    //#[Locked]
    //public $product;

    private $product;

    public $productId;

    #[Computed(persist: true)]
    private function getProduct()
    {
        return Product::whereId($this->productId)->cardFields()->first();
    }

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:17', message: 'Занадто короткий')]
    #[Validate('max:22', message: 'Занадто довгий')]
    public $phone;

    //Специфические правила для отдельных полей
    /*public function rules()
    {
        return [
            'phone' => [
                'required',
                new PhoneNumberRule()
            ],
        ];
    }*/

    public function submit()
    {
        $user = app('user');

        // Валидация данных заказа
        $this->validate($this->getRules());

        // Очистка номера телефона
        $cleanedPhone = PhoneNumberHelper::clean($this->phone);

        // Получаем fingerprint запроса
        $fingerprint = request()->fingerprint();

        // Генерируем уникальный ключ блокировки
        $lockKey = 'quick_order_' . $this->productId . '_' . $cleanedPhone . '_' . $fingerprint;

        // Попытка получения блокировки на уникальный заказ
        $lock = Cache::lock($lockKey, 120); // блокируем на 120 секунд, чтобы предотвратить дубликаты

        if ($lock->get()) {
            try {

                // Получение продукта
                $this->product = $this->getProduct;

                $price = $this->product->getPrice();

                // Создание заказа
                $order = Order::create([
                    "user_id" => $user->id ?? null,
                    "first_name" => $user->first_name ?? "",
                    "last_name" => $user->last_name ?? "",
                    "phone" => $cleanedPhone,
                    "cost" => $price,
                    "cost_without_sale" => $price,
                    "is_quick" => 1,
                    "order_status_id" => 1,
                    "pay_method_id" => 10
                ]);

                OrderProducts::create([
                    "order_id" => $order->id,
                    "product_id" => $this->product->id,
                    "count" => 1,
                    "price" => $price,
                    "base_price" => $price,
                    "base_amount" => $price,
                    "total_amount" => $price,
                ]);

                QuickOrderCreate::dispatch($order);

                // Сохраняем параметры заказа в сессии
                session()->put(["order" => $order]);
                session()->put(["order_id" => $order->id]);
                \Cookie::queue('order_id', $order->id, 5000);
                session()->save();

                // Перенаправляем пользователя на страницу "Спасибо за заказ"
                return redirect()->route('checkout.complete');
            } finally {
                // Автоматическое завершение блокировки через 10 секунд. Ничего не нужно делать здесь.
            }
        } else {
            // Если не удалось получить блокировку, возвращаем ошибку
            return $this->notify(__t('Ви вже зробили подібне замовлення, тому немає потреби робити це знову'), __t('Увага'), 'warning');
        }
    }


    public function render()
    {
        return view('livewire.product.quick-order');
    }
}
