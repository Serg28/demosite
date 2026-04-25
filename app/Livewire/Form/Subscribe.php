<?php

namespace App\Livewire\Form;


use App\Traits\Referrer;
use Livewire\Component;
use App\Models\Subscription;
use App\Traits\LivewireRecaptchable;

use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;

class Subscribe extends Component
{
    use LivewireRecaptchable;

    //use Referrer;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('email', message: 'Це не Email')]
    #[Validate('unique:subscription', message: 'Ви вже підписані')]
    public string|null $email;

    public function subscribe(): void
    {
        $this->validate($this->getRules());
        //dd($this->validate($this->getRules()));
        try {
            $subscribe = Subscription::create($this->only(['email']));
            $this->reset();
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо!"),
                'text' => __t("Ви успішно підписалися на новини!"),
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Щось пішло не так"),
                'text' => __t("Вибачте, виникла помилка. Ми її вже вирішуємо. Спробуйте трохи пізніше."),
                'class' => 'warning'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.form.subscribe');
    }

    public function rendered()
    {
        $this->dispatch('subscribe-initialized');
    }
}
