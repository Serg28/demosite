<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Models\InformationBoard as Board;
use Illuminate\Support\Facades\Cache;

class InfoBoard extends Component
{
    public function render()
    {
        $information = Board::query()->where('is_active','=','1')->orderBy('priority')->first();//->rememberForever()
        return view('livewire.partials.information-board', ['information' => $information]);
    }
}
