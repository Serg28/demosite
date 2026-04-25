<?php

namespace App\Livewire\Product;

use App\Livewire\Profile\Btn\Favorite;
use App\Livewire\Profile\Favorite\Lists as FavoriteList;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: false)]
class FavoriteBtn extends Component
{

    public $product;
    public $class = '';
    private $active;
    public bool $favBtnDell = false;
    private bool $reload = false;

    public function update()
    {

        if (!empty(app('user'))) {
            if ($this->getLike) {
                $this->product->unlike(app('user')->id);
                $this->notify(__t("Товар удален из избранного"),'', 'success');
                $this->dispatch('updateFavoriteList');
            } else {
                $this->product->like(app('user')->id);
                $this->notify(__t("Товар успешно добавлен в избранное"),'', 'success');
            }
            unset($this->getLike);
            $this->dispatch('updateFavoriteCount');
        }else{
            $this->notify(__t("Для перегляду обраного потрібно авторизуватися"),'', 'success');
        }

    }

    public function render()
    {
        return view('livewire.product.favorite.btn.icon', ['active' => $this->getLike, 'class' => $this->class, 'favBtnDell' => $this->favBtnDell]);
    }

    public function placeholder()
    {
        return view('livewire.product.favorite.btn.empty', ['class' => $this->class, 'favBtnDell' => $this->favBtnDell]);
    }

    #[Computed(persist: true)]
    private function getLike(): bool
    {
        return $this->product ? $this->product->checkLike() : false;
    }
}
