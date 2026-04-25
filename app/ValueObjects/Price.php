<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class Price
{
    private string $amount; // Используем строку для точности bcmath

    public function __construct(string|float|int $amount)
    {
        if (is_numeric($amount)) {
            $this->amount = (string)$amount;
        } else {
            throw new InvalidArgumentException('Price must be numeric');
        }

        if (bccomp($this->amount, '0', 2) < 0) {
            throw new InvalidArgumentException('Price cannot be negative');
        }
    }

    public static function from(string|float|int $amount): self
    {
        return new self($amount);
    }

    public function add(Price $other): self
    {
        return new self(bcadd($this->amount, $other->amount, 2));
    }

    public function subtract(Price $other): self
    {
        $result = bcsub($this->amount, $other->amount, 2);
        if (bccomp($result, '0', 2) < 0) {
            throw new InvalidArgumentException('Subtraction result cannot be negative');
        }
        return new self($result);
    }

    public function multiply(float|int $multiplier): self
    {
        return new self(bcmul($this->amount, (string)$multiplier, 2));
    }

    public function divide(float|int $divisor): self
    {
        if ($divisor == 0) {
            throw new InvalidArgumentException('Cannot divide by zero');
        }
        return new self(bcdiv($this->amount, (string)$divisor, 2));
    }

    public function toString(): string
    {
        return $this->amount;
    }

    public function toFloat(): float
    {
        return (float)$this->amount;
    }

    public function toInt(): int
    {
        return (int)bcmul($this->amount, '100', 0); // Копейки/центы
    }

    public function equals(Price $other): bool
    {
        return bccomp($this->amount, $other->amount, 2) === 0;
    }

    public function isGreaterThan(Price $other): bool
    {
        return bccomp($this->amount, $other->amount, 2) > 0;
    }

    public function isLessThan(Price $other): bool
    {
        return bccomp($this->amount, $other->amount, 2) < 0;
    }

    public function __toString(): string
    {
        return $this->amount;
    }
}
