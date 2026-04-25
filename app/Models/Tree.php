<?php

namespace App\Models;

use App\Models\HasMany\Block;
use App\Models\MorphOne\Seo;
use App\Traits\PrepareModelFields;
use App\Traits\SeoCustomUrlTrait;
use App\Traits\SeoTrait;
use App\Traits\SlugUrlFieldTrait;
use App\Traits\UpdateTreeDepth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Usamamuneerchaudhary\Commentify\Traits\Commentable;
use Vis\Builder\Tree as TreeBuilder;

class Tree extends TreeBuilder
{
    use SeoTrait;
    use SeoCustomUrlTrait;
    use SlugUrlFieldTrait;
    use PrepareModelFields;
    use UpdateTreeDepth;
    use Commentable;

    protected $with = ['treemenu'];

    /**
     * Очищение поля url при клонировании модели
     *
     * @param  array|null  $except
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null)
    {
        $clone = parent::replicate($except);
        $clone->url = null;

        return $clone;
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo')->withDefault();
    }

    public function blocks(): MorphMany
    {
        return $this->morphMany(Block::class, 'model')->orderBy('priority', 'asc');
    }

    public function treemenu(): HasOne
    {
        return $this->hasOne(TreeMenu::class, 'tree_id');
    }

    public function menu_(): Collection
    {
        return $this->treemenu()->get();
    }

    public static function getFirstDepthNodes(): Collection
    {
        return self::where('depth', '1')->get();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', '1');
    }

    public function scopePriorityAsc(Builder $query): Builder
    {
        return $query->orderBy('lft', 'asc');
    }

    public function scopeTemplate(Builder $query, string $template): Builder
    {
        return $query->where('template', $template);
    }

    //Из коробки - url = slug
    /*public function getUrl(): string
    {
        return geturl(parent::getUrl(), App::getLocale());
    }*/

    //Мультиязычный урл на случай, если документ имеет привязку к меню (ведет на документ из структуры сайта или на категорию)
    //Можно для этой модели использовать этот вариант
    /*public function getTreeUrl()
    {
        //return ($this->treemenu && !empty($this->treemenu?->menu_id) && !empty($this->treemenu?->menu_type)) ? $this->treemenu->getUrl() : $this->getUrl();

        if($this->treemenu) {
            if (!empty($this->treemenu?->menu_id) && !empty($this->treemenu?->menu_type)) {
                return $this->treemenu->getUrl();
            }

            if(empty($this->treemenu?->menu_id) && !empty($this->treemenu?->t('url'))) {
                return $this->treemenu->getUrl();
            }
            return $this->getUrl();
        }
        return $this->getUrl();
    }*/

    public function getTreeUrl()
    {
        // Получаем URL из treemenu, если условия выполнены, иначе используем основной URL
        if (!empty($this->treemenu?->menu_id) && !empty($this->treemenu?->menu_type)) {
            $url = $this->treemenu->getUrl();
        } elseif (empty($this->treemenu?->menu_id) && !empty($this->treemenu?->t('url'))) {
            $url = $this->treemenu->getUrl();
        } else {
            $url = $this->getUrl();
        }

        // Проверка, если ссылка не начинается с 'http' или '/'
        if (!str_starts_with($url, 'http') && !str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        return $url;
    }

    //Вариант url мультиязычный
    //---------
    public function getUrl($locale = '')
    {
        $locale = ($locale) ?: App::getLocale();
        /*if (!$this->_nodeUrl) {
            $this->_nodeUrl = $this->getGeneratedUrl($locale);
        }*/
        $this->_nodeUrl = $this->getGeneratedUrl($locale);

        if (strpos($this->_nodeUrl, 'http') !== false) {
            return $this->_nodeUrl;
        }

        //return geturl('/' . $this->_nodeUrl, App::getLocale());
        return geturl('/' . $this->_nodeUrl, $locale);
    }

    private function getGeneratedUrlInCache($locale = '')
    {
        $all = $this->getAncestorsAndSelf();
        $slugs = [];

        foreach ($all as $node) {
            $url = ($node->getUrlOrSlug($locale));
            if ($url == '/') {
                continue;
            }

            $slugs[] = $url;
        }
        return implode('/', $slugs);
    }

    public function getGeneratedUrl($locale = '')
    {
        $tags = $this->getCacheTags();
        $locale = ($locale) ?: App::getLocale();
        if ($tags && $this->fileDefinition) {
            return Cache::tags($tags)->rememberForever(
                $this->fileDefinition . '_' . $this->id . '_' . $locale,
                function () use ($locale) {
                    return $this->getGeneratedUrlInCache($locale);
                }
            );
        }

        return $this->getGeneratedUrlInCache($locale);
    }

    //--------------------

    public function getNode(): Tree
    {
        return $this;
    }

}
