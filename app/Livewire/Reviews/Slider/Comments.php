<?php

namespace App\Livewire\Reviews\Slider;

use App\Models\Comment;
use App\Models\Tree;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Comments extends Component
{
    private int $limit = 9;

    public string|null $class = '';

    #[Computed(persist: true)]
    private function comments()
    {
        // Кэшируем комментарии на 5 минут (или другой срок)
        return Cache::remember("comments.tree.latest.{$this->limit}", 300, function () {
            return Comment::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereHasMorph('commentable', [Tree::class]);
                })
                ->with('user', 'children.user', 'children.children')
                ->parent()
                ->latest()
                ->take($this->limit)
                ->get();
        });
    }

    #[Computed]
    public function averageRating()
    {
        // Кэшируем средний рейтинг на 5 минут для тех же фильтров
        return Cache::remember("comments.tree.average_rating", 300, function () {
            return Comment::where('is_active', 1)
                ->whereHasMorph('commentable', [Tree::class])
                ->avg('rating') ?? 0;
        });
    }

    public function render()
    {
        return view('livewire.reviews.slider.comments', [
            'class' => $this->class,
            'comments' => $this->comments,
            'averageRating' => $this->averageRating()
        ]);
    }
}