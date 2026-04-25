<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PayMethod;

/**
 * Класс для расчета оплаты частями и вычисления наценок.
 */
class PayPartsCalculator
{
    // Таблицы наценок для разных методов оплаты (по из слагу)
    private array $commissionRates = [
        'monopayparts' => [
            3 => 2.9,
            4 => 4.1,
            5 => 5.9,
            6 => 7.2,
            7 => 8.3,
            8 => 9.5,
            9 => 10.8,
            10 => 12.0,
            11 => 13.2,
            12 => 14.3,
            13 => 15.5,
            14 => 16.6,
            15 => 17.7,
            16 => 18.8,
            17 => 19.8,
            18 => 20.9,
            19 => 21.9,
            20 => 23.0,
            21 => 24.0,
            22 => 24.9,
            23 => 25.9,
            24 => 26.8,
            25 => 27.8,
        ],
        'privatpayparts' => [
            2 => 1.1,
            3 => 1.7,
            4 => 3.3,
            5 => 5.5,
            6 => 7.1,
            7 => 9.3,
            8 => 11.0,
            9 => 12.75,
            10 => 13.4,
            11 => 13.6,
            12 => 13.8,
            13 => 15.1,
            14 => 16.4,
            15 => 17.6,
            16 => 18.9,
            17 => 19.7,
            18 => 21.0,
            19 => 21.8,
            20 => 23.2,
            21 => 24.0,
            22 => 25.4,
            23 => 26.2,
            24 => 27.0,
            25 => 28.5,
        ],
        // Другие банки можно добавить аналогично
    ];

    /**
     * Получает доступное количество частей для товара по методу оплаты.
     *
     * @param Product $product Товар для проверки.
     * @param string $payMethodSlug Слаг метода оплаты.
     * @param bool $returnAttribute Если true, возвращает атрибут, иначе вызывает метод для получения количества частей.
     * @return int|bool Возвращает количество частей или 0, если недоступно.
     */
    public function getAvailablePartsCount(Product $product, string $payMethodSlug, bool $returnAttribute = false): int|bool
    {
        return match ($payMethodSlug) {
            'monopayparts' => $returnAttribute ? $product->mono_payparts_count : $product->getMonoPartsCount(),
            'privatpayparts' => $returnAttribute ? $product->privat_payparts_count : $product->getPrivatPartsCount(),
            default => 0,
        };
    }

    /**
     * Рассчитывает наценку и итоговую стоимость для товара при выбранном количестве частей и методе оплаты.
     *
     * @param Product $product Товар для расчета.
     * @param int $selectedParts Выбранное количество частей.
     * @param PayMethod|null $selectedPayMethod Выбранный метод оплаты (необязательно).
     * @return array Возвращает массив с информацией о комиссии и итоговой цене.
     */
    public function calculatePriceDetails(Product $product, int $selectedParts, $selectedPayMethod = null): array
    {
        if(!$product || !$selectedParts || !$selectedPayMethod || !$selectedPayMethod->count()) {
            return [];
        }
        $basePrice = $product->getPrice();
        $payMethodSlug = $selectedPayMethod?->checkout?->slug ?? null;

        if(!empty($payMethodSlug)) {
            return $this->calculateFinalPrice($basePrice, $payMethodSlug, $selectedParts, $product);
        }
        return [];
    }

    /**
     * Подготавливает данные для указанного метода оплаты.
     *
     * @param PayMethod $payMethod Метод оплаты.
     * @param bool|int $availablePartsCount Доступное количество частей.
     * @return array Возвращает массив данных для метода оплаты.
     */
    public function preparePaymentData(PayMethod $payMethod, bool|int $availablePartsCount): array
    {
        if (!$payMethod) {
            return [];
        }

        return [
            'name' => $payMethod->t('title'),
            'id' => $payMethod->id,
            'picture' => $payMethod->picture,
            'slug' => $payMethod?->checkout?->slug ?? '',
            'partscount' => $availablePartsCount,
        ];
    }

    /**
     * Рассчитывает наценку и итоговую сумму для указанной цены, метода оплаты и количества частей.
     *
     * @param float $basePrice Базовая цена.
     * @param string $payMethodSlug Слаг метода оплаты.
     * @param int $partsCount Количество частей.
     * @param Product|null $product Товар для проверки (необязательно, для проверки доступных частей).
     * @return array Возвращает массив с суммой комиссии, итоговой ценой и процентом комиссии.
     */
    public function calculateFinalPrice(float $basePrice, string $payMethodSlug, int $partsCount, Product $product = null): array
    {
        // Получаем таблицу наценок для выбранного метода оплаты
        $commissionRates = $this->commissionRates[$payMethodSlug] ?? [];

        // Проверка на обнуление комиссии, если товар и метод оплаты предоставлены
        if ($product && $this->getAvailablePartsCount($product, $payMethodSlug, true) === $partsCount) {
            $commissionRate = 0;
        } else {
            $commissionRate = $commissionRates[$partsCount] ?? 0;
        }

        // Расчет наценки
        $commissionAmount = $basePrice * ($commissionRate / 100);
        $totalPrice = $basePrice + $commissionAmount;

        // Расчет ежемесячной суммы
        $monthlyPayment = $totalPrice / $partsCount;

        return [
            'commissionAmount' => round($commissionAmount, 2),
            'totalPrice' => round($totalPrice, 2),
            'commissionPercentage' => $commissionRate,
            'monthlyPayment' => round($monthlyPayment, 2),
        ];
    }

    /**
     * Рассчитывает наценку и итоговую сумму для указанного товара.
     *
     * @param Product $product Товар для расчета.
     * @param string $payMethodSlug Слаг метода оплаты.
     * @param int $partsCount Количество частей.
     * @return array Возвращает массив с суммой комиссии и итоговой ценой.
     */
    public function calculateForProduct(Product $product, string $payMethodSlug, int $partsCount): array
    {
        $basePrice = $product->getPrice();
        return $this->calculateFinalPrice($basePrice, $payMethodSlug, $partsCount, $product);
    }

    /**
     * Получает все доступные методы оплаты для указанного товара.
     *
     * @param Product $product Товар для проверки.
     * @return array Возвращает массив доступных методов оплаты.
     */
    public function getAvailablePaymentMethodsForProduct(Product $product): array
    {
        $payMethods = PayMethod::with('checkout:id,slug')->installments()->active()->orderPriority()->get();
        $result = [];

        foreach ($payMethods as $payMethod) {
            $availablePartsCount = $this->getAvailablePartsCount($product, $payMethod?->checkout?->slug);
            if ($availablePartsCount) {
                $result[] = $this->preparePaymentData($payMethod, $availablePartsCount);
            }
        }

        return $result;
    }

    /**
     * Проверяет, доступно ли указанное количество частей для метода оплаты.
     *
     * @param string $payMethodSlug Слаг метода оплаты.
     * @param int $partsCount Количество частей.
     * @return bool Возвращает true, если доступно, иначе false.
     */
    public function isPartCountAvailable(string $payMethodSlug, int $partsCount): bool
    {
        return isset($this->commissionRates[$payMethodSlug][$partsCount]);
    }

    /**
     * Получает процент комиссии для указанного метода оплаты и количества частей.
     *
     * @param string $payMethodSlug Слаг метода оплаты.
     * @param int $partsCount Количество частей.
     * @return float Процент комиссии.
     */
    public function getCommissionRate(string $payMethodSlug, int $partsCount): float
    {
        return $this->commissionRates[$payMethodSlug][$partsCount] ?? 0;
    }
}
