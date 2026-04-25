<?php

namespace App\Livewire\Category;

use App\Models\Category;
use App\Services\Category as CategoryService;
use Livewire\Component;

class TopCategories extends Component
{
    protected CategoryService $categoryService;

    public Category $page;

    public function mount(Category $page): void
    {
        $this->page = $page;
    }

    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    public function render()
    {
        $categoriesFiltered = $this->categoryService->getNotEmptyCategories($this->page);

        return view($this->page->getTemplate('partials.livewire-top-categories'),
            compact('categoriesFiltered'));
    }
}
