<?php

namespace App\Models;

class Setting extends \Linecore\Cms\Setting
{
    protected function getResultType($setting): array
    {
        return [
            'text' => $setting->value,
            'text_with_languages' => $setting->t('value_languages'),
            'textarea_with_languages' => $setting->t('textarea_with_languages'),
            'textarea' => $setting->textarea,
            'froala_with_languages' => $setting->t('froala_with_languages'),
            'file' => $setting->file,
            'checkbox' => $setting->check,
        ];
    }
}
