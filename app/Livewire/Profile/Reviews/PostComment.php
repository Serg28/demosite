<?php

namespace App\Livewire\Profile\Reviews;

use App\Models\OrderProducts;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use LivewireUI\Modal\ModalComponent;

class PostComment extends ModalComponent
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $model;

    public $users = [];

    #[Locked]
    public $id;

    public $showDropdown = false;

    protected $numberOfPaginatorsRendered = [];

    public $newCommentState = [
        'body' => '',
        'name' => '',
        'email' => '',
        'minus_text' => '',
        'plus_text' => ''
    ];

    protected $listeners = [
        'refresh' => '$refresh'
    ];

    protected $validationAttributes = [
        'newCommentState.body' => 'Текст відгука',
        'newCommentState.rating' => 'Оцінка',
        'newCommentState.id' => 'Товар'
    ];

    public static function modalMaxWidth(): string
    {
        return 'review-popup';
    }

    public static function modalMaxWidthClass(): string
    {
        return 'review-popup';
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|null
     */
    public function render(): \Illuminate\Foundation\Application|View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application|null
    {
        $this->newCommentState['name'] = $this->newCommentState['name'] ?: app('user')?->first_name ?? '';
        $this->newCommentState['email'] = $this->newCommentState['email'] ?: app('user')?->email ?? '';

        // Получаем текущего пользователя
        $user = app('user');

        return view('livewire.profile.reviews.partials.comment-form', [
            'method'=>'postComment',
            'state'=>'newCommentState',
            'inputId'=> 'comment',
            'inputLabel'=> __t('Написати відгук'),
            'button'=>__t('Надіслати відгук')
        ]);
    }

    /**
     * Handle the comment posting.
     *
     * @return void
     */
    #[On('refresh')]
    public function postComment(): void
    {
        $this->validate([
            'newCommentState.body' => 'required',
            'newCommentState.email' => 'required|email',
            'newCommentState.name' => 'required',
            'newCommentState.rating' => 'required',
            'newCommentState.plus_text' => 'required',
            'newCommentState.minus_text' => 'required',
        ]);

        // Проверка наличия ID и существования товара
        if (!$this->id) {
            session()->flash('error', 'Товар не вибрано');
            return;
        }

        $this->model = Product::find($this->id);
        if (!$this->model) {
            session()->flash('error', 'Товар не знайдений');
            return;
        }

        $user = $this->getUser();

        // Безопасное создание комментария
        $commentData = array_intersect_key($this->newCommentState, array_flip(['body', 'name', 'email', 'minus_text', 'plus_text', 'rating']));
        $comment = $this->model->comments()->make($commentData);
        $comment->user()->associate($user);
        $comment->save();

        // Очистка состояния после добавления комментария
        $this->resetNewCommentState();
        $this->resetPage();

        session()->flash('message', 'Коментар успішно доданий і буде опублікований після схвалення модератором');
        $this->dispatch('openModal', component: 'ModalBlock', arguments: [
            'title' => __t("Дякуємо"),
            'text' => __t("Коментар успішно доданий і буде опублікований після схвалення модератором"),
            'class' => 'success'
        ]);
    }

    /**
     * Retrieve the user for the comment.
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        $user = app('user');
        if (!$user && !empty($this->newCommentState['email'])) {
            $user = User::where('email', $this->newCommentState['email'])->first();
        }

        return $user;
    }

    /**
     * Reset the new comment state.
     *
     * @return void
     */
    protected function resetNewCommentState(): void
    {
        $this->newCommentState = [
            'body' => '',
            'name' => '',
            'email' => '',
            'minus_text' => '',
            'plus_text' => '',
            'rating' => ''
        ];
        $this->users = [];
        $this->showDropdown = false;
    }
}
