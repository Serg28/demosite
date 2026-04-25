<?php

namespace App\Traits;

trait MenuTrait
{
    public function getUrl(): string
    {
        if ($this->tree) {
            return $this->tree->getUrl();
        }

        if ($this->url) {
            return geturl($this->url);
        }

        if ($this->t('url_external')) {
            return $this->t('url_external');
        }
    }

    public static function getMenu(): array
    {
        $menuAll = self::with('tree', 'children')
            ->defaultOrder()
            ->get()
            ->toTree();

        if (isset($menuAll[0])) {
            return $menuAll[0]->children;
        }

        return [];
    }
}
