<?php

namespace App\Traits;

/**
 * Трейт PrepareModelFields предоставляет методы для подготовки и обработки полей модели во время сохранения.
 * В настоящее время добавлен в модели Tree и BaseModel в качестве служебного трейта.
 * Можно добавлять дополнительные методы по мере необходимости.
 */
trait PrepareModelFields
{
    /**
     * Добавляет обработку события saving для фильтрации атрибутов и обработки поля 'url' во время сохранения модели.
     *
     * @return void
     */
    public static function bootPrepareModelFields(): void
    {
        static::saving(static function ($model) {
            $model->filterNonexistentFields();
            $model->processUrlField();
        });
    }

    /**
     * Фильтрует атрибуты модели, оставляя только существующие в базе данных.
     *
     * @return void
     */
    protected function filterNonexistentFields(): void
    {
        $fillableColumns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $this->attributes = array_intersect_key($this->attributes, array_flip($fillableColumns));
    }

    /**
     * Проверяет, что значение поля 'url' содержит пустые строки для всех языков и заменяет на NULL при соответствии условию.
     *
     * @return void
     */
    protected function processUrlField(): void
    {
        // Проверяем, существует ли атрибут 'url'
        if ($this->hasAttribute('url') && $this->attributes['url'] !== null) {
            $siteLanguages = getSiteLanguages()->all();

            $urlField = $this->attributes['url'];
            if (is_string($urlField) && json_decode($urlField, true) === array_fill_keys($siteLanguages, '')) {
                $this->attributes['url'] = null;
            }
        }
    }

    // Добавляем метод hasAttribute, если его еще нет в вашем трейте или базовом классе
    protected function hasAttribute($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

}
