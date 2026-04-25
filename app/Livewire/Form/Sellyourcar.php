<?php

namespace App\Livewire\Form;

use App\Events\SellYourCarCreate;
use App\Models\SellYourCar as ModelSellYourCar;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

class Sellyourcar extends Component {

    use WithFileUploads;

    #[Rule('required|between:2,64')]
    public string|null $name;

    #[Rule('required|between:4,22')]
    public string|null $phone;

    #[Rule('required|email')]
    public string|null $email;

    #[Rule('required|between:2,2048')]
    public string|null $comment;

    #[Rule('required')]
    public array $checkbox;

    #[Rule(['picture.*' => 'image|max:2048'])]
    public array $picture = [];

    #[Rule(['file.*' => 'image|max:2048'])]
    public array $file = [];

    #[Rule('required|between:2,32')]
    public string|null $vin;

    #[Rule('required|between:2,32')]
    public string|null $regdate;

    #[Rule('required|between:2,32')]
    public string|null $mileage;

    #[Rule('required|between:2,64')]
    public string|null $model;


    public string|null $g_recaptcha_response;
    public mixed $recaptcha;
    public string|null $formId;
    public string|null $referrer;

    public string|null $subject;

    public int|null $user_id;

    public function mount($recaptcha = true, string $formId = '', string $subject = 'empty') {
        $this->checkbox = [1];
        $this->user_id = app('user')->id ?? null;
        $this->recaptcha = !(($recaptcha === 'false' || $recaptcha === false));
        $this->formId = $formId ?: strtolower(class_basename($this)).'_component';
        $this->referrer = geturl(\Request::fullUrl());
        $this->subject = $subject;
    }
    public function rendered(){
        $this->dispatch('sellyourcar-initialized');
    }

    #[On('recaptcha-changed')]
    public function setCaptcha($value): void
    {
        $this->g_recaptcha_response = $value;
    }

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

        $this->validate([
            'name' => 'required|between:2,64',
            'phone' => 'required|between:4,22',
            'vin' => 'required|between:4,22',
            'model' => 'required|between:1,36',
            'regdate' => 'required|between:5,10',
            'mileage' => 'required|between:1,36',
            'checkbox' => 'required',
            'picture' => 'image|max:2048',
            'file' => 'image|max:2048',
            'g_recaptcha_response' => $this->recaptcha ? 'required|recaptcha' : '',
        ]);
        $_picture = [];
        //$path = 'forms/sellyourcar/'.md5(time());
        $path = md5(time());

        foreach ($this->picture as $pic) {
            $image = $pic->store($path, 'sellyourcar');
            $_picture[] = str_replace(base_path()."/public",'',public_path('storage/sellyourcar/'.$image));
        }

        $picture = '["'.implode('","',$_picture).'"]';
        $data = $this->except(['g_recaptcha_response','checkbox','recaptcha','file']);
        $data['picture'] = $picture;
        $sellYourCar = ModelSellYourCar::create($data);
        SellYourCarCreate::dispatch($sellYourCar);
        $this->resetForm();
        session()->flash('success', 'Спасибо, форма отправлена');
    }

    public function render() {
        return view('livewire.form.sellyourcar');
    }

    private function resetForm(): void {
        $subject = $this->subject;
        $this->reset();
        $this->subject = $subject;
    }
}
