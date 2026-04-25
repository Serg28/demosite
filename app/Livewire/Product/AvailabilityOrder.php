<?php

namespace App\Livewire\Product;

use App\Mail\AvailabilityOrderMail;
use App\Traits\LivewireRecaptchable;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;
use App\Models\AvailabilityOrder as AvailabilityOrderModel;

class AvailabilityOrder extends ModalComponent
{
    use LivewireRecaptchable;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто коротке')]
    #[Validate('max:50', message: 'Занадто довге')]
    public string|null $name = null;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:17', message: 'Занадто короткий')]
    #[Validate('max:22', message: 'Занадто довгий')]
    public string|null $phone = null;

    #[Validate('required')]
    #[Validate('email')]
    public string|null $email = null;

    #[Validate('required')]
    public $product_id = null;

    public function mount($product_id)
    {
        $this->phone = $this->phone ?: (app('user')->phone ?? '');
        $this->name = $this->name ?: (app('user')->first_name ?? '');
        $this->email = $this->email ?: (app('user')->email ?? '');
        $this->product_id = $product_id ?: null;
    }

    public static function modalMaxWidth(): string
    {
        return 'pre-order';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'pre-order';
    }

    public function submit()
    {
        $this->validate($this->getRules());

        $fields = $this->except(['_token', 'g_recaptcha_response']);

        $fields['user_id'] = app('user')?->id ?? null;

        try {

            $order = AvailabilityOrderModel::create($fields);

            Mail::to(settingForMail('email-administratora'))->send(new AvailabilityOrderMail($order));

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо"),
                'text' => __t("Вашу заявку успішно відправлено! Ви отримаєте повідомлення на вказаний email, як тільки товар з'явиться у наявності"),
                'class' => 'success'
            ]);

            $this->reset();

        } catch (\Exception $e) {
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Сталася помилка"),
                'text' => __t("Щось пішло не так. Ми вже вирішуємо проблему. Спробуйте знову за кілька хвилин."),
                'class' => 'error'
            ]);
        }

        //$this->reset();
    }

    public function render()
    {
        return view('livewire.product.availability-order');
    }
}
