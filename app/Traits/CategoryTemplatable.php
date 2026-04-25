<?php

namespace App\Traits;

/**
 * Трейт CategoryTemplatable
 *
 * Этот трейт предоставляет функциональность для получения префикса шаблона на основе типа товаров,
 * хранящихся в категории. Тип используется для выбора шаблона Blade (категории), используемого для вывода категорий в сайте.
 *
 * Пример использования:
 *
 * 1) Добавляем в таблицу категории поле type (или другое, указанное в свойстве $typeProductFieldName)
 * 2) В app/Cms/Definitions/Categories.php  добавляем поле для указания типа товара, хранящихся в категории
 * 3) В модели категории добавляем
 * use CategoryTemplatable;
 *
 * И далее у модели появляется атрибут $model->category_template_name; с типом товара, который можно использовать как часть названия шаблона.
 * Для упрощения в модели категории добавлен метод getCategoryTemplateType() который безопасно возвращает этот атрибут, даже если трейт не используется в модели.
 *
 * Этот атрибут можно использовать для определения, из какой папки брать blade-шаблоны для вывода категорий в сайте.
 * Напр., если тип товара в категории - auto, то берем шаблоны из папки auto и т.д.
 *
 */
trait CategoryTemplatable
{
    /**
     * Имя поля для типа продукта в базе данных.
     *
     * @var string
     */
    protected string $typeProductFieldName = 'product_type';

    /**
     * Значение префикса шаблона по умолчанию, используемое, когда поле типа отсутствует или пусто.
     *
     * @var string|null
     */
    protected string|null $defaultTemplatePrefixValue = null;

    /**
     * Статическое свойство для кэширования значений.
     *
     * @var array
     */
    protected static array $categoryTemplateNameCache = [];

    /**
     * Получить всех предков текущей модели с непустым полем типа продукта.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentsOfType()
    {
        return $this->ancestors()->select($this->typeProductFieldName)
            ->whereNotNull($this->typeProductFieldName)
            ->where($this->typeProductFieldName, '!=', '')
            ->get();
    }
    /*   public function getParentsOfType()
    {
        return $this->getAncestorsAndSelf()
            ->filter(function ($item) {
                return !is_null($item->{$this->typeProductFieldName}) && $item->{$this->typeProductFieldName} !== '';
            });
    }
    */

    /**
     * Проверить, присутствует ли колонка типа продукта в атрибутах модели.
     *
     * @return bool
     */
    protected function hasTypeColumn(): bool
    {
        //return isset($this->attributes[$this->typeProductFieldName]);
        return isset($this->{$this->typeProductFieldName}) ?? false;
    }

    /**
     * Получить атрибут с именем шаблона категории на основе типа продукта.
     *
     * @return string|null
     */
    public function getCategoryTemplateNameAttribute(): ?string
    {
        $className = get_class($this);

        // Проверяем, было ли значение кэшировано для текущей модели
        if (!isset(self::$categoryTemplateNameCache[$className])) {
            // Получаем значение атрибута и кэшируем его
            self::$categoryTemplateNameCache[$className] = $this->calculateCategoryTemplateName();
        }

        return self::$categoryTemplateNameCache[$className];
    }

    /**
     * Вычислить значение атрибута категории по типу продукта.
     *
     * @return string|null
     */
    protected function calculateCategoryTemplateName(): ?string
    {
        // Проверить, присутствует ли колонка типа в атрибутах модели
        if (!$this->hasTypeColumn()) {
            return $this->defaultTemplatePrefixValue;
        }

        // Если у текущей модели есть непустой тип продукта, вернуть его
        if (!empty($this->{$this->typeProductFieldName})) {
            return $this->{$this->typeProductFieldName};
        }

        // Если трейт не используется или у всех предков нет непустого типа продукта,
        // вернуть значение префикса шаблона по умолчанию
        $ancestors = $this->getParentsOfType();
        foreach ($ancestors as $ancestor) {
            // Проверяем, есть ли у предка непустой тип продукта
            if (!empty($ancestor->{$this->typeProductFieldName})) {
                return $ancestor->{$this->typeProductFieldName};
            }
        }

        return $this->defaultTemplatePrefixValue;
    }
}



