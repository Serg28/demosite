<?php

namespace App\Services;

use App\Models\Product;

//Класс для работы с функционалом оплаты частями.
//Реализует методы получения количества частей оплаты товара для разных методов оплаты
//формирование списка
class PaymentInstallment {

    public function getInstallmentCountForProduct(Product $product, string $payMethodSlug): int|bool
    {
        return match ($payMethodSlug) {
            "monopayparts" => $product->getMonoPartsCount(),
            "privatpayparts" => $product->getPrivatPartsCount(),
            default => 0,
        };
    }

    public function preparePaymentData($payMethod, $availablePartsCount): array
    {
        if (!$payMethod) {
            return [];
        }

        return [
            "name" => $payMethod->t("title"),
            "id" => $payMethod->id,
            "picture" => $payMethod->picture,
            "slug" => $payMethod->checkout->slug,
            "partscount" => $availablePartsCount,
        ];
    }
}
