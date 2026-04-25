<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

final class ProductsHasCharacteristicRule implements Rule
{
    private int $characteristicId;

    private ?Collection $failedProducts;

    public function __construct(int $characteristicId)
    {
        $this->characteristicId = $characteristicId;
    }

    public static function make(int $characteristicId): self
    {
        return new self($characteristicId);
    }

    public function passes($attribute, $value): bool
    {
        $this->failedProducts = Product::find(\explode(',', $value))
            ->filter(function (Product $product) {
                return ! $product->characteristics->first(function (ProductCharacteristicOption $characteristicOption) {
                    return $characteristicOption->characteristic_id == $this->characteristicId;
                });
            });

        return $this->failedProducts->isEmpty();
    }

    public function message(): string
    {
        $titles = $this->failedProducts->map(static function (Product $product) {
            return $product->t('title');
        })->implode(', ');

        return __t("Товары $titles не имеют данной характеристики");
    }
}
