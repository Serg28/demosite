<?php

namespace App\Livewire\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

/**
 * Reusable pagination for Livewire components.
 *
 * Usage in mount():
 *   $this->bootPagination($model->getUrl());
 *
 * Usage in computed property:
 *   return $this->makePaginator($items, $total);
 *
 * paginatorPath is derived once from the canonical model URL to avoid
 * request()->path() returning the Livewire AJAX endpoint on subsequent updates.
 * paginatorQuery is captured once from the initial GET request for the same reason.
 */
trait HasPagination
{
    #[Locked]
    public string $paginatorPath = '';

    #[Locked]
    public array $paginatorQuery = [];

    public int $page = 1;

    public int $perPage = 24;

    protected function bootPagination(string $canonicalUrl): void
    {
        $this->paginatorPath  = rtrim((string) parse_url($canonicalUrl, PHP_URL_PATH), '/');
        $this->paginatorQuery = request()->except('page');
    }

    #[On('page-changed')]
    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    protected function makePaginator(array $items, int $total): LengthAwarePaginator
    {
        return (new LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $this->perPage,
            currentPage: $this->page,
        ))
            ->withPath($this->paginatorPath)
            ->appends($this->paginatorQuery);
    }
}
