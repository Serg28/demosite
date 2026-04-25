<?php

namespace App\Livewire\Reviews\Company;

use App\Models\User;
use App\Services\Sort\DateSorting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Usamamuneerchaudhary\Commentify\Http\Livewire\Comments as BaseComments;

class Comments extends BaseComments
{
    protected string $paginationTheme = 'bootstrap';

    public $newCommentState = [
        'body' => '',
        'name' => '',
        'email' => ''
    ];

    private DateSorting $sortService;

    public function boot()
    {
        $this->sortService = new DateSorting();
    }

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
            ->when($this->sortService->getOrderField(), function ($query) {
                return $query->orderBy($this->sortService->getOrderField(), $this->sortService->getOrderDirect());
            })
            ->paginate(config('commentify.pagination_count',10));

        return view('livewire.reviews.company.comments', [
            'filter' => $this->sortService,
            'comments' => $comments,
            'ratingData' => $this->ratingData
        ]);
    }

    #[Computed(persist: true)]
    private function ratingCounts()
    {
        $cacheKey = 'comments_rating_counts_' . $this->model->id;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return $this->model->comments()
                ->selectRaw('rating, COUNT(*) as count')
                ->where('is_active', 1) // учитывать только активные комментарии
                ->groupBy('rating')
                ->pluck('count', 'rating');
        });
    }

    #[Computed(persist: true)]
    private function ratingData()
    {
        $ratingCounts = $this->ratingCounts();
        $totalVotes = $ratingCounts->sum();

        // Подготавливаем данные для отображения
        return [
            5 => [
                'label' => __t('Відмінно'),
                'count' => $ratingCounts[5] ?? 0,
                'percentage' => $totalVotes > 0 ? ($ratingCounts[5] ?? 0) / $totalVotes * 100 : 0,
                'color' => '#24AC05'
            ],
            4 => [
                'label' => __t('Добре'),
                'count' => $ratingCounts[4] ?? 0,
                'percentage' => $totalVotes > 0 ? ($ratingCounts[4] ?? 0) / $totalVotes * 100 : 0,
                'color' => '#88E074'
            ],
            3 => [
                'label' => __t('Середньо'),
                'count' => $ratingCounts[3] ?? 0,
                'percentage' => $totalVotes > 0 ? ($ratingCounts[3] ?? 0) / $totalVotes * 100 : 0,
                'color' => '#FFA41C'
            ],
            2 => [
                'label' => __t('Так собі'),
                'count' => $ratingCounts[2] ?? 0,
                'percentage' => $totalVotes > 0 ? ($ratingCounts[2] ?? 0) / $totalVotes * 100 : 0,
                'color' => '#7F90B1'
            ],
            1 => [
                'label' => __t('Погано'),
                'count' => $ratingCounts[1] ?? 0,
                'percentage' => $totalVotes > 0 ? ($ratingCounts[1] ?? 0) / $totalVotes * 100 : 0,
                'color' => '#FF6565'
            ],
        ];
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
            'email' => ''
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
