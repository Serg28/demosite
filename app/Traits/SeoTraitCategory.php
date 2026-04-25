<?php

namespace App\Traits;

use App\Models\CharacteristicOption;
use App\Services\Filters\Filter;

trait SeoTraitCategory
{
    use SeoTrait;

    public function getSeoTitle(Filter $filter = null): string
    {
        $title = '';

        if ($this->seo && $this->seo->t('seo_title')) {
            $title = strip_tags($this->seo->t('seo_title'));
        } elseif ($this->t('title')) {
            $title = strip_tags($this->t('title')).' '.__t('title_seo_end');
        }

        return $this->getReplacedText($filter, $title);
    }

    public function getSeoDescription(Filter $filter = null): string
    {
        $description = '';

        if ($this->seo && $this->seo->t('seo_description')) {
            $description = strip_tags($this->seo->t('seo_description'));
        } elseif ($this->t('short_description')) {
            $description = strip_tags($this->t('short_description')).' '.__t('title_seo_end');
        }

        return $this->getReplacedText($filter, $description);
    }

    private function filterValue(Filter $filter): string
    {
        static $cachedValue;

        // Если значение уже было рассчитано, вернуть его
        if ($cachedValue !== null) {
            return $cachedValue;
        }

        $arrayOptions = [];
        $category = null;

        foreach ($filter->getSelectedFilters() as $category => $valueArray) {
            $filters = CharacteristicOption::leftJoin('characteristics', 'characteristics.id', '=', 'characteristic_options.characteristic_id')
                ->whereIn('characteristic_options.slug', $valueArray)
                ->where('characteristics.slug', $category)
                ->select('characteristic_options.*')
                ->rememberForever()->cacheTags(['characteristic_options'])
                ->get();

            foreach ($filters as $item) {
                $arrayOptions[] = $item->t('title');
                $category = $item->characteristicCache()->t('title');
            }
        }

        // Сохраняем рассчитанное значение в статической переменной
        $cachedValue = $arrayOptions && $category ? mb_strtolower($category.' '.implode(',', $arrayOptions)) : '';

        return $cachedValue;
    }

    private function getReplacedText(?Filter $filter, string $text): string
    {
        $textReplaceFilter = $filter && $filter->getCountFilterSelected() == 1 ? $this->filterValue($filter) : '';

        return str_replace('[filter]', $textReplaceFilter, $text);
    }








    protected function getSeo()
    {
        if (!$this->seoLoaded) {
            // Здесь загружаете $this->seo из базы данных или откуда нужно
            $this->loadedSeo = // Ваш код загрузки $this->seo;
            $this->seoLoaded = true;
        }

        return $this->loadedSeo;
    }
}
