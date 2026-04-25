<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Vis\Builder\Models\Language;

trait LocalizedPrice
{

    /**
     * Возвращает цену или цену для текущего языка сайта.
     *
     * Если цена представлена в формате JSON с поддержкой мультиязычности,
     * метод вернет цену, соответствующую текущему языку сайта.
     * В противном случае будет возвращена стандартная цена.
     *
     * @return int
     */
    public function getRawPriceAttribute(): int
    {
        $rawPrice = Str::isJson($this->price) ? $this->t('price') : $this->price;
        return (int)$rawPrice;
    }

    /**
     * Вовзращает старую цену или старую цену для текущего языка сайта.
     *
     * Если старая цена представлена в формате JSON с поддержкой мультиязычности,
     * метод вернет старую цену, соответствующую текущему языку сайта.
     * В противном случае будет возвращена стандартная старая цена.
     *
     * @return int
     */
    public function getRawOldPriceAttribute(): int
    {
        $rawOldPrice = Str::isJson($this->price_old) ? $this->t('price_old') : $this->price_old;
        return (int)$rawOldPrice;
    }

    /**
     * Возвращает конвертированную цену на основе логики преобразования.
     *
     * Метод вызывается из модели для получения конвертированной цены
     * на основе необработанной цены, представленной в формате JSON или стандартном формате.
     *
     * @return int
     */
    public function getTraitPrice(): int
    {
        return $this->getPriceConvert($this->rawPrice);
    }

    /**
     * Вовзращает конвертированную старую цену на основе логики преобразования.
     *
     * Метод вызывается из модели для получения конвертированной старой цены
     * на основе необработанной старой цены, представленной в формате JSON или стандартном формате.
     *
     * @return int
     */
    public function getTraitOldPrice(): int
    {
        return $this->getPriceConvert($this->rawOldPrice);
    }


    /**
     * Вовзращает мультиязычные цены товара для каждого языка.
     *
     * Метод получает сырую цену или старую цену товара (в зависимости от параметра $price)
     * и формирует ассоциативный массив с мультиязычными ценами для каждого языка сайта.
     *
     * @param string $price Сырая цена или старая цена (price или price_old)
     * @return array Ассоциативный массив с мультиязычными ценами для каждого языка сайта
     */
    /*private function getPrices($price): array
    {
        $prices = [];
        $languages = getSiteLanguages();

        foreach ($languages as $lang) {
            $prices[$lang] = $this->getTextFromJson($price, $lang);
        }

        return $prices;
    }*/
    private function getPrices($price): array
    {
        $languages = getSiteLanguages()->toArray();

        $prices = [];

        foreach ($languages as $lang) {
            $prices[$lang] = (float)$this->getTextFromJson($price, $lang) ?: 0;
        }

        return $prices;
    }

    /**
     * Возвращает мультиязычные актуальные цены товара для каждого языка сайта.
     *
     * Метод получает мультиязычные цены товара на основе сырой цены (price)
     * и возвращает ассоциативный массив с мультиязычными ценами для каждого языка сайта.
     *
     * @return array Ассоциативный массив с мультиязычными ценами для каждого языка сайта
     */
    public function getPricesList(): array
    {
        return $this->getPrices($this->price);
    }

    /**
     * Возвращает мультиязычные старые цены товара для каждого языка сайта.
     *
     * Метод получает мультиязычные старые цены товара на основе сырой старой цены (price_old)
     * и возвращает ассоциативный массив с мультиязычными старыми ценами для каждого языка сайта.
     *
     * @return array Ассоциативный массив с мультиязычными старыми ценами для каждого языка сайта
     */
    public function getOldPricesList(): array
    {
        return $this->getPrices($this->price_old);
    }

}
