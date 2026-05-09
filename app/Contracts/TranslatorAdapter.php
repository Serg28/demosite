<?php

namespace App\Contracts;

interface TranslatorAdapter
{
    public function translate(string $text, string $from, string $to): string;

    public function translateBatch(array $texts, string $from, string $to): array;
}
