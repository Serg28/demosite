<?php

namespace App\Livewire\News;

use App\Services\Sort\DateSorting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Repository\NewsRepository;
use Livewire\WithPagination;
use Livewire\Attributes\Locked;

/**
 * Класс News
 * @package App\Livewire\News
 *
 * Компонент Livewire, отвечающий за отображение новостей.
 */
class News extends Component
{
    use WithPagination;

    /** @var mixed $tag Тег новостей. */
    private $tag;

    /** @var mixed $tags Все теги новостей. */
    private $tags;

    /** @var mixed $tagName Имя тега новостей. */
    private $tagName;

    /** @var int|null $pageId ID категории новостей. */
    #[Locked]
    public ?int $pageId;

    /** @var NewsRepository $newsRepository Репозиторий новостей. */
    private NewsRepository $newsRepository;

    /** @var int $root Корневая категория. */
    private int $root = 10;

    /** @var string|null $sorts Метод сортировки. */
    #[Url]
    public ?string $sort;

    #[Locked]
    public $dats;

    private bool $more = false;
    private int $perpage = 8;

    #[Url(history: true)]
    public $page;

    private DateSorting $sortService;

    /**
     * Монтирует компонент.
     *
     * @param int|null $pageId ID текущей страницы.
     * @return void
     */
    public function mount(int $pageId = null): void
    {
        $this->pageId = ($pageId !== $this->root) ? $pageId : 0;
        $this->page = request('page', 1);
        //$this->more = false;
        //$this->tag = request('tag');
    }

    /**
     * Инициализирует NewsRepository.
     *
     * @param NewsRepository $newsRepository
     * @return void
     */
    public function boot(NewsRepository $newsRepository): void
    {
        $this->newsRepository = $newsRepository;
        $this->newsRepository->perpage = $this->perpage;
        $this->sortService = new DateSorting();
    }

    public function updatingPage($page): void
    {
        if ($page !== $this->page) {
            $this->page = $page;
        }
    }

    #[Computed]
    private function categories()
    {
        return $this->newsRepository->getAllCategories($this->root);
    }

    /**
     * Отображает компонент.
     *
     * @return View
     */
    public function render()
    {
        $this->setPage($this->page);

        // Определяем параметры сортировки
        $orderField = $this->sortService->getOrderField();
        $orderDirect = $this->sortService->getOrderDirect();
        $countShow = $this->sortService->getCountShow();

        // Определяет соответствующие новости на основе фильтров категории и тега.
        if ($this->tag && $this->pageId) {
            $news = $this->newsRepository->filterByCategoryAndTag($this->pageId, $this->tag, $orderField, $orderDirect);
        } elseif ($this->tag && !$this->pageId) {
            $news = $this->newsRepository->filterByTag($this->tag, $orderField, $orderDirect);
        } elseif (!$this->tag && $this->pageId) {
            $news = $this->newsRepository->filterByCategory($this->pageId, $orderField, $orderDirect);
        } else {
            $news = $this->newsRepository->filterAllNews($orderField, $orderDirect);
        }

        if ($this->more) {
            $this->dats = $this->dats->push(...$news->items());
        } else {
            $this->dats = collect($news->items());
        }

        return view('livewire.news.news', [
            'list' => $this->dats,
            'news' => $news,
            'categories' => $this->categories(),
            'currentUrl' => currentUrl(),
            'filter' => $this->sortService
        ]);
        //$tags = Tag::active()->get();
        //$tagname = ($this->tag) ? $tags->where('slug', $this->tag)->first() : '';
    }

    public function showMore()
    {
        $this->more = true;
        $this->page++;
    }
}
