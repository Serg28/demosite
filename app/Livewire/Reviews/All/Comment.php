<?php

namespace App\Livewire\Reviews\All;


use App\Livewire\Reviews\Comment as BaseComment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class Comment extends BaseComment
{
    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        return view('livewire.reviews.all.comment', ['isEditing' => $this->isEditing]);
    }
}
