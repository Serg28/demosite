<?php

namespace App\Livewire\Reviews\All;

use Usamamuneerchaudhary\Commentify\Http\Livewire\Comments as BaseComments;
use App\Models\Tree;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Usamamuneerchaudhary\Commentify\Models\Comment as ModelsComment;

class Comments extends BaseComments
{

    protected string $paginationTheme = 'bootstrap';

    private $limit = 10;

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        // Получаем все комментарии
        $comments = ModelsComment::where('is_active', 1) // Фильтр по активным комментариям
        ->where(function ($query) {
            $query->whereHasMorph('commentable', 'product') // Фильтр по модели товара
            ->orWhereHasMorph('commentable', [Tree::class]); // Фильтр по модели Tree
        })
            ->with('user', 'children.user', 'children.children') // Жадная загрузка связанных данных
            ->latest() // Сортировка по последним комментариям
            ->parent()
            ->paginate($this->limit); // Пагинация

        return view('livewire.reviews.all.comments', [
            'comments' => $comments
        ]);
    }

}
