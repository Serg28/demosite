<?php

namespace Tests\Unit\Unit\ValueObjects;

use App\ValueObjects\PriceTier;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PriceTierTest extends TestCase
{
    public function test_creates_from_array(): void
    {
        $tier = PriceTier::from(['min_qty' => 10, 'rate' => 0.95]);

        $this->assertSame(10, $tier->minQty);
        $this->assertSame(0.95, $tier->rate);
    }

    public function test_price_for_returns_discounted_price(): void
    {
        $tier = PriceTier::from(['min_qty' => 10, 'rate' => 0.9]);

        $this->assertSame(90.0, $tier->priceFor(100.0));
    }

    public function test_discount_percent(): void
    {
        $tier = PriceTier::from(['min_qty' => 10, 'rate' => 0.9]);

        $this->assertSame(10, $tier->discountPercent());
    }

    public function test_throws_on_invalid_min_qty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PriceTier(0, 0.9);
    }

    public function test_throws_on_invalid_rate_above_one(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PriceTier(1, 1.5);
    }

    public function test_throws_on_zero_rate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PriceTier(1, 0.0);
    }

    public function test_rate_of_one_is_valid(): void
    {
        $tier = new PriceTier(1, 1.0);
        $this->assertSame(0, $tier->discountPercent());
    }
}
