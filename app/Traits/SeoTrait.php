<?php
namespace App\Traits;

use App\Models\CharacteristicOption;
use App\Models\MorphOne\Seo;
use App\Models\SeoGroups;
use App\Models\SeoUrls;
use App\Services\Filters\Filter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

trait SeoTrait
{
    protected $seoGroup;
    protected $seoDefault;
    private $seoLoaded = false;
    private $loadedSeo;

    public function getProductFields(): array
    {
        return [
            "{code}" => $this->code,
            "{title}" => $this->t("title"),
            "{price}" => method_exists($this, 'getPrice') ? $this->getPrice() . ' ' . setting("currency") : '',
            "{parent}" => method_exists($this, 'getParentCategoryName') ? $this->getParentCategoryName() : ''
        ];
    }

    public function setSeoGroups($viewPath): void
    {
        if ($viewPath) {
            $seoGroup = SeoGroups::with(["seo"])->remember(5 * 60)->cacheTags(['seogroups'])
                ->where("view", $viewPath)
                ->active()
                ->first();
            $this->seoGroup = $seoGroup ? $seoGroup->seo : null;
        }
    }

    public function setDefaultSeo(): void
    {
        $seoDefault = SeoGroups::with(["seo"])->remember(5 * 60)->cacheTags(['seogroups'])
            ->where("view", "default_seo_tags.empty")
            ->active()
            ->first();
        $this->seoDefault = $seoDefault ? $seoDefault->seo : null;
    }

    public function getSeoTitle(?Filter $filter = null): string
    {
        $title = $this->getSeoValue("seo_title") ?: $this->getDefaultSeoValue("seo_title") ?: $this->t("title");
        return $this->getReplacedText($filter, strip_tags($title));
    }

    public function getSeoDescription(?Filter $filter = null): string
    {
        $description = $this->getSeoValue("seo_description") ?: $this->getDefaultSeoValue("seo_description") ?: $this->t("short_description");
        return $this->getReplacedText($filter, strip_tags($description));
    }

    public function getSeoH1(): string
    {
        return strip_tags(
            $this->getSeoValue("seo_h1") ?:
                $this->getDefaultSeoValue("seo_h1") ?:
                    $this->t("title")
        );
    }

    public function getSeoKeywords(): string
    {
        return strip_tags(
            $this->getSeoValue("seo_keywords") ?:
                $this->getDefaultSeoValue("seo_keywords") ?:
                    $this->t("seo_keywords")
        );
    }

    public function getSeoText(): string
    {
        return $this->getSeoValue("seo_text") ?: "";
    }

    public function getSeoPicture(): string
    {
        if ($this->picture) {
            return $this->getImgPath(600, 314);
        }
        return asset(glide(setting("seo_logo"), ["w" => 600, "h" => 314]));
    }

    public function getSeoCanonical(): string
    {
        return strip_tags(
            $this->getSeoValue("seo_canonical") ?:
                $this->getDefaultSeoValue("seo_canonical") ?:
                    request()->url()
        );
    }

    private function getSeoValue($field): ?string
    {
        $placeholders = $this->getProductFields();
        return $this->getSeoField($field, $placeholders);
    }

    private function getDefaultSeoValue($field): ?string
    {
        $placeholders = $this->getProductFields();
        $this->setDefaultSeo();
        return $this->getSeoField($field, $placeholders);
    }

    private function getSeoField($field, $placeholders): ?string
    {
        $seo = $this->_getSeoUrl();

        $seos = collect([$seo, $this->seoGroup, $this->seoDefault]);

        return $seos
            ->map(function ($seo) use ($field, $placeholders) {
                return $seo && !empty($seo->t($field))
                    ? str_replace(
                        array_keys($placeholders),
                        array_values($placeholders),
                        $seo->t($field)
                    )
                    : null;
            })
            ->filter()
            ->first();
    }

    private function _getSeoUrl()
    {
        $locale = App::getLocale();
        $requestUri = currentUrl();
        $requestUriPath = currentUrlPath();

        return once(function () use ($locale, $requestUri, $requestUriPath) {
            if ($seoUrl = SeoUrls::where('link->' . $locale, 'like', $requestUri)
                ->orWhere('link->' . $locale, 'like', trim($requestUri, '/'))
                ->orWhere('link->' . $locale, 'like', $requestUriPath)
                ->orWhere('link->' . $locale, 'like', '/' . $requestUriPath)
                ->orWhere('link->' . $locale, 'like', trim($requestUriPath, '/'))
                ->active()->first()) {
                $this->seo = $seoUrl->seo;
                return $seoUrl->seo;
            }
            return $this->seo;
        });
    }

    private function getReplacedText(?Filter $filter, string $text): string
    {
        if (!$filter || $filter->getCountFilterSelected() !== 1) {
            return $text;
        }

        return str_replace('{filter}', $this->filterValue($filter), $text);
    }

    private function filterValue(Filter $filter): string
    {
        static $cachedValue;

        if ($cachedValue !== null) {
            return $cachedValue;
        }

        $arrayOptions = [];
        $category = null;

        foreach ($filter->getSelectedFilters() as $categorySlug => $valueArray) {
            $filters = CharacteristicOption::leftJoin('characteristics', 'characteristics.id', '=', 'characteristic_options.characteristic_id')
                ->whereIn('characteristic_options.slug', $valueArray)
                ->where('characteristics.slug', $categorySlug)
                ->select('characteristic_options.*')
                ->remember(5 * 60) // Кэшируем результат на 5 минут (5 * 60 секунд)
                ->cacheTags(['characteristic_options'])
                ->get();

            foreach ($filters as $item) {
                $arrayOptions[] = $item->t('title');
                $category = $item->characteristicCache()->t('title');
            }
        }

        $cachedValue = $arrayOptions && $category ? mb_strtolower($category . ' ' . implode(',', $arrayOptions)) : '';

        return $cachedValue;
    }

    public function scopeSeoIndex($query)
    {
        return $query->whereHas("seo", function ($query) {
            $query->where("is_seo_noindex", "!=", 1);
        });
    }
}
