<?php

namespace App\Livewire\News;

use Livewire\Component;

class CommentsCount extends Component
{
    public $rowId;

    public $count;

    public function mount($rowId = 0) {
        $this->rowId = $rowId;

        $this->count = rand(1,100);
    }

    public function render()
    {
        return view('livewire.news.comments-count', [
            'count' => $this->count
        ]);
    }
    public function rendered(){
        $this->dispatch('news-comments-count-initialized');
    }
}
