<?php

namespace App\Adapters\Delivery;

use App\Contracts\TranslatorAdapter;

class NullTranslatorAdapter implements TranslatorAdapter
{
    public function translate(string $text, string $from, string $to): string
    {
        return $text;
    }

    public function translateBatch(array $texts, string $from, string $to): array
    {
        return $texts;
    }
}
