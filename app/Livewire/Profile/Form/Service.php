<?php

namespace App\Livewire\Profile\Form;

use App\Traits\LivewireRecaptchable;
use App\Traits\Referrer;
use Livewire\Component;
use Livewire\WithFileUploads;

use Livewire\Attributes\Validate;

class Service extends Component {
    use WithFileUploads;
    use LivewireRecaptchable;
    use Referrer;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:64', message: 'Занадто довгий')]
    public string|null $pib;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:4', message: 'Занадто короткий')]
    #[Validate('max:22', message: 'Занадто довгий')]
    public string|null $phone = '';

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:2048', message: 'Занадто довгий')]
    public string|null $comment;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:2048', message: 'Занадто довгий')]
    public string|null $complect;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:32', message: 'Занадто довгий')]
    public string|null $order_nom;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:256', message: 'Занадто довгий')]
    public string|null $product_name;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:32', message: 'Занадто довгий')]
    public string|null $serial;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:32', message: 'Занадто довгий')]
    public string|null $statement;

    #[Validate('required', message: "Обов'язкове поле")]
    #[Validate('min:2', message: 'Занадто короткий')]
    #[Validate('max:32', message: 'Занадто довгий')]
    public string|null $date;

    //#[Rule(['picture.*' => 'image|max:2048'])]
    //#[Validate(['picture.*' => 'mimes:jpg,gif,png,mov,mp4|max:10240'], message: ['mimes' => 'формат .jpg, .gif, .png, .mov, .mp4', 'max:10240' => 'розмір файлу до 10 МБ' ])]
    public array $picture = [];

    //#[Rule(['file.*' => 'image|max:2048'])]
    //#[Validate(['file.*' => 'mimes:jpg,gif,png,mov,mp4|max:10240'], message: ['mimes' => 'формат .jpg, .gif, .png, .mov, .mp4', 'max:10240' => 'розмір файлу до 10 МБ' ])]
    public array $file = [];
    //public string|null $g_recaptcha_response;
    //public mixed $recaptcha;
    //public string|null $formId;
    //public string|null $referrer;

    public string|null $subject;

    public int|null $user_id;

    //public function mount(string $subject = 'empty') {

        //$this->user_id = app('user')->id ?? null;
        //$this->recaptcha = !(($recaptcha === 'false' || $recaptcha === false));
        //$this->formId = $formId ?: strtolower(class_basename($this)).'_component';
        //$this->referrer = geturl(\Request::fullUrl());
        //$this->subject = $subject;
    //}

    /*
    #[On('recaptcha-changed')]
    public function setCaptcha($value): void
    {
        $this->g_recaptcha_response = $value;
    }*/

    public function deletePicture(int $index): void
    {
        unset($this->picture[$index]);
    }

    public function updated(): void
    {
        if(!empty($this->file)){
            $this->picture = array_merge($this->picture, $this->file);
            unset($this->file);
        }
    }

    public function submit() {

        $this->validate($this->getRules());

        try {
            $_picture = [];
            $path = md5(time());

            foreach ($this->picture as $pic) {
                $image = $pic->store($path, 'profile_service');
                $_picture[] = str_replace(base_path()."/public",'',public_path('storage/service_aplication/'.$image));
            }

            $picture = '["'.implode('","',$_picture).'"]';
            $data = $this->except(['g_recaptcha_response','checkbox','recaptcha','file']);
            $data['picture'] = $picture;

            //TODO: куда отравлять данные заявки?

            $this->resetForm();

            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Дякуємо"),
                'text' => __t("Вашу заявку успішно відправлено"),
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка во время отправки формы запроса на сервис: ' . $e->getMessage());
            $this->dispatch('openModal', component: 'ModalBlock', arguments: [
                'title' => __t("Щось пішло не так"),
                'text' => __t("Вибачте, виникла помилка. Ми її вже вирішуємо. Спробуйте трохи пізніше."),
                'class' => 'error'
            ]);
        }

        session()->flash('success', 'Вашу заявку успішно відправлено');
    }

    private function resetForm(): void {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }

    public function render()
    {
        return view('livewire.profile.form.service');
    }
}
