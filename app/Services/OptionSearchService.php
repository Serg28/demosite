<?php

namespace App\Services;

use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * OptionSearchService — быстрый поиск среди опций фильтра.
 *
 * Для 1M товаров с 500+ опциями на фасет нужна отдельная стратегия:
 * - Индекс опций кэшируется (Redis)
 * - Поиск работает на сервере (не браузер)
 * - Результаты кэшируются на 30min
 */
class OptionSearchService
{
    private const INDEX_CACHE_TTL = 1440; // 24 часа
    private const SEARCH_CACHE_TTL = 30; // 30 минут
    private const SEARCH_LIMIT = 15; // Максимум результатов
    private const MIN_QUERY_LENGTH = 2;

    /**
     * Поиск опций в характеристике.
     *
     * @param int $characteristicId
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchOptions(int $characteristicId, string $query, int $limit = self::SEARCH_LIMIT): array
    {
        $query = trim($query);

        if (strlen($query) < self::MIN_QUERY_LENGTH) {
            return [];
        }

        $cacheKey = "option_search:{$characteristicId}:" . md5($query);

        return Cache::remember($cacheKey, now()->addMinutes(self::SEARCH_CACHE_TTL), function () use ($characteristicId, $query, $limit) {
            return $this->performSearch($characteristicId, $query, $limit);
        });
    }

    /**
     * Построить индекс опций для быстрого поиска.
     * Запускается один раз в день (ночью).
     */
    public function indexCharacteristicOptions(Characteristic $characteristic): void
    {
        $cacheKey = $this->getIndexCacheKey($characteristic->id);

        // Получаем все опции
        $options = $characteristic->options()
            ->select(['id', 'title', 'slug', 'value'])
            ->get();

        // Нормализуем для быстрого поиска
        $indexed = [];
        foreach ($options as $option) {
            $title = $option->title['ua'] ?? $option->title['ru'] ?? '';
            $normalized = $this->normalizeText($title);

            $indexed[] = [
                'id' => $option->id,
                'title' => $title,
                'slug' => $option->slug,
                'value' => $option->value,
                'normalized' => $normalized,
                'tokens' => $this->tokenize($normalized), // Для токен-базированного поиска
            ];
        }

        // Кэшируем индекс
        Cache::remember($cacheKey, now()->addMinutes(self::INDEX_CACHE_TTL), fn() => $indexed);
    }

    /**
     * Выполнить поиск по индексу.
     */
    private function performSearch(int $characteristicId, string $query, int $limit): array
    {
        $cacheKey = $this->getIndexCacheKey($characteristicId);
        $index = Cache::get($cacheKey, []);

        if (empty($index)) {
            // Если индекс не построен, строим на лету
            $characteristic = Characteristic::findOrFail($characteristicId);
            $this->indexCharacteristicOptions($characteristic);
            $index = Cache::get($cacheKey, []);
        }

        $query = $this->normalizeText($query);
        $results = [];

        foreach ($index as $option) {
            $score = $this->calculateMatchScore($query, $option);

            if ($score > 0) {
                $results[] = [
                    'id' => $option['id'],
                    'title' => $option['title'],
                    'slug' => $option['slug'],
                    'score' => $score,
                ];
            }
        }

        // Сортируем по релевантности
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // Ограничиваем результаты
        return array_slice($results, 0, $limit);
    }

    /**
     * Рассчитать скор совпадения для сортировки.
     */
    private function calculateMatchScore(string $query, array $option): float
    {
        $score = 0;
        $normalized = $option['normalized'];

        // Точное совпадение (начало слова) — максимальный приоритет
        if (str_starts_with($normalized, $query)) {
            $score += 100;
        }

        // Совпадение в середине слова
        if (str_contains($normalized, $query)) {
            $score += 50;
        }

        // Токен-базированный поиск (для многосложных слов)
        foreach ($option['tokens'] as $token) {
            if (str_starts_with($token, $query)) {
                $score += 25;
            }
        }

        // Levenshtein расстояние (для опечаток)
        $distance = levenshtein($query, $normalized);
        if ($distance <= 2 && $distance > 0) {
            $score += (2 - $distance) * 10; // Максимум 20 за близкое совпадение
        }

        return $score;
    }

    /**
     * Нормализовать текст для поиска.
     */
    private function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        // Удаляем диакритические знаки
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text) ?? $text;
        // Удаляем спецсимволы кроме дефиса
        $text = preg_replace('/[^a-z0-9\s\-]/i', '', $text);
        // Удаляем лишние пробелы
        $text = preg_replace('/\s+/', ' ', trim($text));

        return $text;
    }

    /**
     * Разбить текст на токены (слова).
     */
    private function tokenize(string $text): array
    {
        return array_filter(explode(' ', $text), fn($token) => strlen($token) > 0);
    }

    /**
     * Получить статистику по диапазону для Range Slider.
     */
    public function getRangeStats(Characteristic $characteristic): ?array
    {
        if (!$characteristic->is_range_type) {
            return null;
        }

        $cacheKey = "range_stats:{$characteristic->id}";

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($characteristic) {
            $values = [];

            // Извлекаем числовые значения из всех опций
            $characteristic->options()->get()->each(function ($option) use (&$values) {
                $title = $option->title['ua'] ?? $option->title['ru'] ?? '';
                $number = $this->extractNumber($title);

                if ($number !== null) {
                    $values[] = $number;
                }
            });

            if (empty($values)) {
                return null;
            }

            sort($values);

            return [
                'min' => min($values),
                'max' => max($values),
                'avg' => array_sum($values) / count($values),
                'count' => count($values),
            ];
        });
    }

    /**
     * Извлечь число из строки.
     */
    private function extractNumber(string $text): ?float
    {
        // Ищем первое число в строке
        if (preg_match('/(-?\d+(?:[.,]\d+)?)/', $text, $matches)) {
            $number = str_replace(',', '.', $matches[1]);
            return (float) $number;
        }

        return null;
    }

    /**
     * Получить диапазон опций в промежутке (для Range Slider).
     *
     * @param Characteristic $characteristic
     * @param float $min
     * @param float $max
     * @return array ID опций
     */
    public function getOptionIdsInRange(Characteristic $characteristic, float $min, float $max): array
    {
        $cacheKey = "range_ids:{$characteristic->id}:" . md5("{$min}-{$max}");

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($characteristic, $min, $max) {
            return $characteristic->options()
                ->get()
                ->filter(function ($option) use ($min, $max) {
                    $number = $this->extractNumber(
                        $option->title['ua'] ?? $option->title['ru'] ?? ''
                    );

                    return $number !== null && $number >= $min && $number <= $max;
                })
                ->pluck('id')
                ->toArray();
        });
    }

    /**
     * Очистить индекс опций (при обновлении).
     */
    public function clearIndex(Characteristic $characteristic): void
    {
        Cache::forget($this->getIndexCacheKey($characteristic->id));
    }

    /**
     * Генерировать ключ кэша индекса.
     */
    private function getIndexCacheKey(int $characteristicId): string
    {
        return "option_index:{$characteristicId}";
    }
}
