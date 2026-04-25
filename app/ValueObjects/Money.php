<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private Price $amount;
    private string $currency;

    public function __construct(Price|float|int|string $amount, string $currency = 'USD')
    {
        $this->amount = $amount instanceof Price ? $amount : Price::from($amount);
        
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidArgumentException('Currency must be a valid 3-letter ISO code');
        }
        
        $this->currency = $currency;
    }

    public static function from(Price|float|int|string $amount, string $currency = 'USD'): self
    {
        return new self($amount, $currency);
    }

    public function getAmount(): Price
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot add {$other->currency} to {$this->currency}"
            );
        }

        return new self($this->amount->add($other->amount), $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot subtract {$other->currency} from {$this->currency}"
            );
        }

        return new self($this->amount->subtract($other->amount), $this->currency);
    }

    public function multiply(float|int $multiplier): self
    {
        return new self($this->amount->multiply($multiplier), $this->currency);
    }

    public function divide(float|int $divisor): self
    {
        return new self($this->amount->divide($divisor), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->currency === $other->currency && 
               $this->amount->equals($other->amount);
    }

    public function isGreaterThan(Money $other): bool
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot compare different currencies');
        }
        return $this->amount->isGreaterThan($other->amount);
    }

    public function isLessThan(Money $other): bool
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot compare different currencies');
        }
        return $this->amount->isLessThan($other->amount);
    }

    public function toString(): string
    {
        return "{$this->amount} {$this->currency}";
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
