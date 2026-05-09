<?php

namespace App\Http\ViewComposers;

use App\Services\BreadcrumbsService;
use Illuminate\View\View;

/**
 * Хлібні крихти для сторінки значення характеристики.
 * Аналог BreadcrumbsOptionsComposer з linecore-demo.
 * View повинен мати: $characteristic (Characteristic), $valueName (string).
 */
class BreadcrumbsCharacteristicComposer
{
    public function __construct(private readonly BreadcrumbsService $breadcrumbs) {}

    public function compose(View $view): void
    {
        $characteristic = $view->getData()['characteristic'] ?? null;
        $valueName = $view->getData()['valueName'] ?? '';

        if ($characteristic === null) {
            return;
        }

        $view->with('breadcrumbs', $this->breadcrumbs->forCharacteristic($characteristic, $valueName));
    }
}
