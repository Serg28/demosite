<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function t(string $field): string
    {
        $value = $this->{$field};

        if (! is_array($value)) {
            return (string) ($value ?? '');
        }

        $locale = App::getLocale();

        if (! empty($value[$locale])) {
            return $value[$locale];
        }

        foreach (config('site.title_locales', ['ua', 'ru', 'en']) as $fallback) {
            if (! empty($value[$fallback])) {
                return $value[$fallback];
            }
        }

        return '';
    }
}
