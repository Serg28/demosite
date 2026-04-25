<?php

namespace App\Livewire\Favorite;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class Count extends Component
{
    #[On('updateFavoriteCount')]
    public function updateFavoriteCount()
    {
        $this->render();
    }

    public function render()
    {
        $user = app('user'); // Получаем текущего авторизованного пользователя

        $favoriteCount = (!empty($user)) ? Product::whereLikedBy($user->id)->count() : 0;

        return view('livewire.favorite.count', ['count' => $favoriteCount ]);
    }

    public function placeholder()
    {
        return view('livewire.favorite.count-empty');
    }
}
