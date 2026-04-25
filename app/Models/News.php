<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class News extends BaseModel
{
    protected $table = 'news';

    public function getUrl($locale = ''): string
    {
        //return route('blog', ['slug' => $this->slug]); // из коробки: урл = slug
        return geturl(route('blog', $this->getUrlOrSlug($locale))); //мультиязычный урл
        //return geturl('/blog/' . $this->getUrlOrSlug(), $locale); //мультиязычный урл


        /* вложенность url */
        //$category = Tree::where('id',$this->category->id)->active()->firstOrFail();
        //return route('blog', ['slug' => $category->slug.'/'.$this->slug]);
        /* --------------- */
    }

    public function getNode()
    {
        return $this->category; //вложенный путь к новости (напр., в хлебных крошках)
        //return Tree::where('template', 'news')->first(); //одноуровневый путь к новости  (напр., в хлебных крошках)
    }

    public function date(): string
    {
        return $this->humanDate();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    //---
    public function category(): HasOne
    {
        return $this->hasOne(Tree::class, 'id', 'tree_id');
    }

    public function getTreeForMenu(int $parent = 0): array
    {
        $nodes = Tree::where('parent_id', '=', $parent)->orWhere('id','=',$parent)->defaultOrder()->get()->toTree(); //вместе с корневой категорией новостей
//        $nodes = Tree::where(
//            'parent_id',
//            '=',
//            $parent
//        )->defaultOrder()->get()->toTree(); //без корневой категории новостей
        $data = [];

        $traverse = function ($categories, $prefix = ' - ') use (&$traverse, &$data): void {
            foreach ($categories as $category) {
                $data[$category->id] = $prefix . ' ' . $category->t('title');
                $traverse($category->children, $prefix . $category->t('title') . ' / ');
            }
        };
        $traverse($nodes);

        return $data;
    }

    //---

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getAuthor(): string
    {
        return $this->author ? $this->author->getFullName() : '';
    }

    public function getLastNews(int $count = 3): Collection
    {
        return News::active()->latest()->where('id', '!=', $this->id)->limit($count)->get();
    }

    public function products() {
        return $this->belongsTo(Product::class);
        //return $this->belongsToMany(Product::class, 'news_products');
           // ->withPivot('category_id');
    }
}
