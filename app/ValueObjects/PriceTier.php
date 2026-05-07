<?php

namespace App\ValueObjects;

use InvalidArgumentException;

/**
 * Рівень оптової ціни: мінімальна кількість + коефіцієнт знижки.
 *
 * Зберігається в categories.wholesale_tiers як JSON:
 * [{"min_qty": 10, "rate": 0.95}, {"min_qty": 50, "rate": 0.90}]
 */
final readonly class PriceTier
{
    public function __construct(
        public int $minQty,
        public float $rate,
    ) {
        if ($minQty < 1) {
            throw new InvalidArgumentException('PriceTier: minQty must be >= 1');
        }

        if ($rate <= 0 || $rate > 1) {
            throw new InvalidArgumentException('PriceTier: rate must be in (0, 1]');
        }
    }

    public static function from(array $data): self
    {
        return new self(
            minQty: (int) ($data['min_qty'] ?? 1),
            rate:   (float) ($data['rate'] ?? 1.0),
        );
    }

    /** Ціна за одиницю при даному рівні. */
    public function priceFor(float $basePrice): float
    {
        return round($basePrice * $this->rate, 2);
    }

    /** Відсоток знижки відносно базової ціни. */
    public function discountPercent(): int
    {
        return (int) round((1 - $this->rate) * 100);
    }
}
