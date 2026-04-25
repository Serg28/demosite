<?php

namespace App\Livewire\Profile\Btn;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class Favorite extends Component
{
    public $favoriteCount = 0;

    public function mount()
    {
        $user = app('user'); // Получаем текущего авторизованного пользователя

        // Если пользователь авторизован, получаем количество избранных товаров
        if ($user) {
            $this->favoriteCount = Product::whereLikedBy($user->id)->count();
        }
    }

    #[On('updateFavoriteCount')]
    public function updateFavoriteCount()
    {
        $this->render();
    }

    public function render()
    {
        $user = app('user'); // Получаем текущего авторизованного пользователя
        if (!empty(app('user'))) {
            $this->favoriteCount = Product::whereLikedBy($user->id)->count();
        }
        return view('livewire.profile.btn.favorite', ['favoriteCount' => $this->favoriteCount ]);
    }
}
