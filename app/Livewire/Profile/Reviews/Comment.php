<?php

namespace App\Livewire\Profile\Reviews;


use App\Livewire\Reviews\Product\Application;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Usamamuneerchaudhary\Commentify\Models\User;
use Usamamuneerchaudhary\Commentify\Http\Livewire\Comment as BaseComment;

class Comment extends BaseComment
{
    /**
     * @param $isEditing
     * @return void
     */
    public function updatedIsEditing($isEditing): void
    {
        if (!$isEditing) {
            return;
        }
        $this->editState = [
            'body' => $this->comment->body
        ];
    }

    /**
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editComment(): void
    {
        $this->authorizeForUser(app('user'),'update', $this->comment);
        //$this->authorize('update', $this->comment);
        $this->validate([
            'editState.body' => 'required|min:2'
        ]);
        $this->comment->update($this->editState);
        $this->isEditing = false;
        $this->showOptions = false;
        $this->notify(__t("Коментар успішно оновлено"), __t('Дякуємо'), 'success');
    }

    /**
     * @return void
     * @throws AuthorizationException
     */
    #[On('refresh')]
    public function deleteComment(): void
    {
        $this->authorizeForUser(app('user'),'destroy', $this->comment);
        //$this->authorize('destroy', $this->comment);
        $this->comment->delete();
        $this->showOptions = false;
        $this->dispatch('refresh');
        $this->notify(__t("Коментар успішно видалено"), __t('Спасибо'), 'success');
    }

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        return view('livewire.profile.reviews.comment');
    }

    /**
     * @return void
     */
    #[On('refresh')]
    public function postReply(): void
    {
        if (!$this->comment->isParent()) {
            return;
        }
        $this->validate([
            'replyState.body' => 'required',
            'replyState.email' => 'required',
            'replyState.name' => 'required',
        ]);
        $reply = $this->comment->children()->make($this->replyState);
        $reply->user()->associate(app('user'));
        $reply->commentable()->associate($this->comment->commentable);
        $reply->user_id = !empty($reply->user->id) ? $reply->user->id : null;
        $reply->is_active = 0;
        $reply->save();

        $this->replyState = [
            'body' => ''
        ];
        $this->isReplying = false;
        $this->showOptions = false;
        $this->dispatch('refresh')->self();
        $this->notify(__t("Коментар успішно доданий"), __t('Дякуємо'), 'success');
    }

    /**
     * @param $userName
     * @return void
     */
    public function selectUser($userName): void
    {
        if ($this->replyState['body']) {
            $this->replyState['body'] = preg_replace('/@(\w+)$/', '@'.str_replace(' ', '_', Str::lower($userName)).' ', $this->replyState['body']);
            $this->users = [];
        } elseif ($this->editState['body']) {
            $this->editState['body'] = preg_replace('/@(\w+)$/', '@'.str_replace(' ', '_', Str::lower($userName)).' ', $this->editState['body']);
            $this->users = [];
        }
    }


    /**
     * @param $searchTerm
     * @return void
     */
    #[On('getUsers')]
    public function getUsers($searchTerm): void
    {
        if (!empty($searchTerm)) {
            $this->users = User::where('name', 'like', '%'.$searchTerm.'%')->take(5)->get();
        } else {
            $this->users = [];
        }
    }

}
