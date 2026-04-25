<?php

namespace App\Repository;

use App\Models\News;
use App\Models\Tree;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class NewsRepository extends BaseRepository
{
    public $perpage = 8;

    public function getModelName(): string
    {
        return News::class;
    }

    public function filterByTag(?string $tag, ?string $sortBy = null, ?string $sortOrder = 'desc'): Paginator
    {
        $query = $this->model::active()->whereHas('tags', function ($query) use ($tag): void {
            $query->slug($tag);
        });

        return $this->applySorting($query, $sortBy, $sortOrder)->paginate($this->perpage);
    }

    public function filterByCategory(?string $category, ?string $sortBy = null, ?string $sortOrder = 'desc'): Paginator
    {
        $query = $this->model::with('category')->active()->whereHas(
            'category',
            function ($query) use ($category): void {
                $query->where('tree_id', $category);
            }
        );

        return $this->applySorting($query, $sortBy, $sortOrder)->paginate($this->perpage);
    }

    public function filterByCategoryAndTag(?string $category, ?string $tag, ?string $sortBy = null, ?string $sortOrder = 'desc'): Paginator
    {
        $query = $this->model::with('category')->active()->whereHas(
            'category',
            function ($query) use ($category): void {
                $query->where('tree_id', $category);
            }
        )->whereHas('tags', function ($query) use ($tag): void {
            $query->slug($tag);
        });

        return $this->applySorting($query, $sortBy, $sortOrder)->paginate($this->perpage);
    }

    public function filterAllNews(?string $sortBy = null, ?string $sortOrder = 'desc'): Paginator
    {
        $query = $this->model::with('category')->whereHas('category', function ($query): void {
            $query->where('is_active', 1);
        })->active();

        return $this->applySorting($query, $sortBy, $sortOrder)->paginate($this->perpage);
    }

    /**
     * @param array<string,string> $with
     */
    public function findBySlug(string $slug, array $with = []): Model
    {
        //return $this->model::with($with)->where('slug', $slug)->active()->firstOrFail();
        return $this->model::with($with)->slug($slug)->active()->firstOrFail();
    }

    public function findByCategory(int $tree_id, array $with = []): Model
    {
        return $this->model::with($with)->where('tree_id', $tree_id)->active()->firstOrFail();
    }

    public function findByCategoryAndSlug(string $tree_slug, string $slug, array $with = []): Model
    {
        return $this->model::with($with)->where(['tree_id' => $tree_slug, 'slug' => $slug])->active()->firstOrFail();
    }

    public function getCategoryBySlug(string $root_category)
    {
        //return Tree::where('slug', $root_category)->active();
        return Tree::slug($root_category)->active();
    }

    public function findCategoryBySlug(string $root_category)
    {
        //return Tree::where('slug', $root_category)->active()->firstOrFail();
        return Tree::slug($root_category)->active()->firstOrFail();
    }

    public function getAllCategories(int $root_category)
    {
        //return Tree::where('slug',$category)->active()->firstOrFail();
        //return $this->model->getTreeForMenu($root_category);
        return Tree::where('parent_id', '=', $root_category)->active()->defaultOrder()->get();
    }

    public function getLatest(int $count = 3, ?int $expectId = null): Collection
    {
        return $this->model::latest()->where('id', '!=', $expectId)->active()->limit($count)->get();
    }

    public function getSimilarNewsByTag(News $news, int $count): Collection
    {
        $tagList = collect();

        foreach ($news->tags as $tag) {
            $tagList = $tagList->merge($tag->news->where('id', '!=', $news->id));
        }

        $getCountTagsNews = $tagList->countBy('id')->sortDesc()->take($count)->keys();

        return $this->model::whereIn('id', $getCountTagsNews)->active()->get();
    }

    public function findById(int $id, array $columns = ['*'], array $with = []): Model
    {
        // TODO: Implement findById() method.
    }

    public function findOneByAttribute(string $attribute, string $value, array $columns = ['*']): Model
    {
        // TODO: Implement findOneByAttribute() method.
    }

    public function findWhere(
        array $where,
        array $columns = ['*'],
        array $with = []
    ): \Illuminate\Database\Eloquent\Collection {
        // TODO: Implement findWhere() method.
    }

    public function getCollectionWhereIn(
        string $attribute,
        array $values,
        array $columns = ['*']
    ): \Illuminate\Database\Eloquent\Collection {
        // TODO: Implement getCollectionWhereIn() method.
    }

    public function getCollectionWhereBetween(
        string $attribute,
        array $values,
        array $columns = ['*']
    ): \Illuminate\Database\Eloquent\Collection {
        // TODO: Implement getCollectionWhereBetween() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    protected function applySorting($query, ?string $sortBy, ?string $sortOrder)
    {
        if ($sortBy) {
            return $query->orderBy($sortBy, $sortOrder);
        }

        return $query->latest();
    }

}
