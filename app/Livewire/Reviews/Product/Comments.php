<?php

namespace App\Livewire\Reviews\Product;

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
        'plus_text' => ''
    ];

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(): \Illuminate\Foundation\Application|View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|null
    {

        $this->newCommentState['name'] = $this->newCommentState['name'] ?: app('user')?->first_name ?? '';
        $this->newCommentState['email'] = $this->newCommentState['email'] ?: app('user')?->email ?? '';

        $comments = $this->model
            ->comments()->where('is_active', 1)
            ->with('user', 'children.user', 'children.children')
            ->parent()
            ->latest()
            ->paginate(config('commentify.pagination_count',10));

        return view('livewire.reviews.product.comments', [
            'comments' => $comments
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
        ];
        $this->users = [];
        $this->showDropdown = false;

        $this->dispatch('closeModal');
        $this->resetPage();
        session()->flash('message', 'Коментар успішно доданий і буде опублікований після схвалення модератором');
        //$this->notify(__t("Коментар успішно доданий і буде опублікований після схвалення модератором"), __t('Дякуємо'), 'success');
        $this->dispatch('openModal', component: 'ModalBlock', arguments: [
            'title' => __t("Дякуємо"),
            'text' => __t("Коментар успішно доданий і буде опублікований після схвалення модератором"),
            'class' => 'success'
        ]);
    }

    public function getUser()
    {
        $user = app('user');
        if(!$user) {
            $user = User::where('email', $this->newCommentState['email'])->first();
        }

        return $user;
    }
}
