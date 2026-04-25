<?php

namespace App\Livewire\Profile\Reviews;

use App\Models\Order;
use App\Models\OrderProducts;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Usamamuneerchaudhary\Commentify\Http\Livewire\Comments as BaseComments;

class Comments extends BaseComments
{
    protected string $paginationTheme = 'bootstrap';

    public $newCommentState = [
        'body' => '',
        'name' => '',
        'email' => '',
        'minus_text' => '',
        'plus_text' => '',
        'rating' => '',
    ];

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(): \Illuminate\Foundation\Application|View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|null
    {
        $this->newCommentState['name'] = $this->newCommentState['name'] ?: app('user')?->first_name ?? '';
        $this->newCommentState['email'] = $this->newCommentState['email'] ?: app('user')?->email ?? '';

        // Получаем текущего пользователя
        $user = app('user');

        // Извлекаем уникальные продукты с комментариями текущего пользователя и пагинацией
        $productsWithComments = OrderProducts::query()
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['product.comments' => function ($query) use ($user) {
                $query->/*where('is_active', 1)->*/where('user_id', $user->id); // Фильтруем комментарии по текущему пользователю
            }])
            ->select('product_id')
            ->groupBy('product_id') // Уникальные продукты
            ->paginate(config('commentify.pagination_count', 10)); // Пагинация

        return view('livewire.profile.reviews.comments', [
            'productsWithComments' => $productsWithComments,
            'user' => $user,
        ]);
    }

    /**
     * @return void
     */
    #[On('refresh')]
    public function postComment(): void
    {
        $this->validate([
            'newCommentState.body' => 'required',
            'newCommentState.email' => 'required',
            'newCommentState.name' => 'required',
        ]);

        $user = $this->getUser();

        $comment = $this->model->comments()->make($this->newCommentState);
        $comment->user()->associate($user);
        $comment->save();

        $this->newCommentState = [
            'body' => '',
            'name' => '',
            'email' => '',
            'minus_text' => '',
            'plus_text' => '',
            'rating' => '',
        ];
        $this->users = [];
        $this->showDropdown = false;

        $this->dispatch('closeModal');
        $this->resetPage();
        session()->flash('message', 'Коментар успішно доданий і буде опублікований після схвалення модератором');
        $this->dispatch('openModal', component: 'ModalBlock', arguments: [
            'title' => __t("Дякуємо"),
            'text' => __t("Коментар успішно доданий і буде опублікований після схвалення модератором"),
            'class' => 'success'
        ]);
    }

    public function getUser()
    {
        $user = app('user');
        if (!$user) {
            $user = User::where('email', $this->newCommentState['email'])->first();
        }

        return $user;
    }
}
