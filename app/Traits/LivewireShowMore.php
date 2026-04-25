<?php

namespace App\Traits;

use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

trait LivewireShowMore {

    protected string $paginationTheme = 'bootstrap-with-showmore';

    #[Url(as: 'page')]
    public $p;

    #[Locked]
    public $list;
    //private $list;

    private bool $more = false;

    public function mountLivewireShowMore(): void
    {
        $this->p = request('page', 1);
    }

    public function showMore(): void
    {
        $this->more = true;
        $this->p++;
        $this->setPage($this->p);
    }

    public function updatingPage($p): void
    {
        if ($p !== $this->p) {
            $this->p = $p;
        }
    }

    public function handleLivewireShowMore($products)
    {
        if ($this->more) {
            return $this->list->push(...$products['products']);
        }

        return $this->list = collect($products['products']->items());
    }
}