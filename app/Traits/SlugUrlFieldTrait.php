<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait SlugUrlFieldTrait
{

    /**
     * Проверяем url модели. Если заполнено поле url для текущего языка, ищем по нему. Иначе по slug
     * @param $query
     * @param $slug
     * @return mixed
     */
    public function scopeSlug($query, $slug): mixed
    {
        return $query->when(
            !empty($slug),
            function ($query) use ($slug) {
                $locale = App::getLocale();
                $query->where(function ($query) use ($slug, $locale) {
                    $query->where(function ($query) use ($slug, $locale) {
                        $query->whereNotNull("{$locale}_url")
                            ->where("{$locale}_url", $slug);
                    })->orWhere(function ($query) use ($slug, $locale) {
                        $query->where(function ($query) use ($slug, $locale) {
                            $query->whereNull("{$locale}_url")
                                ->orWhere("{$locale}_url", '');
                        })->where('slug', $slug);
                    });
                });
            }
        );

        return $query;
    }

    public function scopeSlugIn($query, array $slugs): mixed
    {
        return $query->when(
            !empty($slugs),
            function ($query) use ($slugs) {
                $locale = App::getLocale();
                $query->where(function ($query) use ($slugs, $locale) {
                    $query->where(function ($query) use ($slugs, $locale) {
                        $query->whereNotNull("{$locale}_url")
                            ->whereIn("{$locale}_url", $slugs);
                    })->orWhere(function ($query) use ($slugs, $locale) {
                        $query->where(function ($query) use ($slugs, $locale) {
                            $query->whereNull("{$locale}_url")
                                ->orWhere("{$locale}_url", '');
                        })->whereIn('slug', $slugs);
                    });
                });
            }
        );

        return $query;
    }

    /**
     * Получение локализованного URL или slug.
     *
     * @param bool|string $locale
     * @return string|null
     */
    public function getUrlOrSlug(bool|string $locale = false): ?string
    {
        $url = $this->getVirtualUrlByLocale($locale);
        return ($url) ?: $this->slug ?? '';
    }

    /**
     * Получение виртуального URL по локали.
     *
     * @param bool|string $locale
     * @return string|null
     */
    protected function getVirtualUrlByLocale(bool|string $locale): ?string
    {
        $locale = $locale ?: App::getLocale();

        return $this->getAttribute("{$locale}_url");
    }

    /**
     * Получение полного URL с учетом локализации и фильтров.
     *
     * @param bool|string $locale
     * @return string|array
     */
    public function _getFullUrl(bool|string $locale = false): array|string
    {
        // Получаем текущий URL
        $currentUrl = request()->fullUrl();

        // Если указана локаль, выполняем локализацию URL
        if ($locale) {
            $currentPath = $this->getUrl();
            $localizedPath = $this->getUrl($locale);

            // Инициализируем массивы для замены
            $replaceFrom = [$currentPath];
            $replaceTo = [$localizedPath];

            // Если метод filter существует, добавляем фильтры для замены
            if (method_exists($this, 'filter')) {
                $filters = $this->filter()->init();
                $replaceFrom[] = $filters->getUrlFiltersWithoutShow();
                $replaceTo[] = $filters->getLocalizedUrlFilters($locale);
            }

            // Выполняем замену одним вызовом str_replace
            $currentUrl = str_replace($replaceFrom, $replaceTo, $currentUrl);
        }

        return $currentUrl;
    }

    /**
     * Получение полного URL с учетом локализации и фильтров.
     *
     * @param bool|string $locale
     * @return string|array
     */
    public function getFullUrl(bool|string $locale = false): array|string
    {
        // Получаем текущий URL из браузера
        $currentUrl = request()->fullUrl();

        // Если указана локаль, выполняем локализацию URL
        if ($locale) {
            $currentPath = $this->getUrl();
            $localizedPath = $this->getUrl($locale);

            // Инициализируем массивы для замены
            [$replaceFrom, $replaceTo] = $this->getReplacementArrays($currentPath, $localizedPath, $locale);

            // Выполняем замену одним вызовом str_replace
            $currentUrl = str_replace($replaceFrom, $replaceTo, $currentUrl);
        }

        return $currentUrl;
    }

    /**
     * Получение локализованного slug.
     *
     * @return string
     */
    public function getLocalizedSlugAttribute(): string
    {
        return $this->getUrlOrSlug() ?? '';
    }

    /**
     * Получение массивов для замены в URL.
     *
     * @param string $currentPath
     * @param string $localizedPath
     * @param bool|string $locale
     * @return array
     */
    protected function getReplacementArrays(string $currentPath, string $localizedPath, bool|string $locale): array
    {
        $replaceFrom = [$currentPath];
        $replaceTo = [$localizedPath];

        // Если метод filter существует, добавляем фильтры для замены
        if (method_exists($this, 'filter')) {
            $filters = $this->filter()->init();
            $replaceFrom[] = $filters->getUrlFiltersWithoutShow();
            $replaceTo[] = $filters->getLocalizedUrlFilters($locale);
        }

        return [$replaceFrom, $replaceTo];
    }
}
