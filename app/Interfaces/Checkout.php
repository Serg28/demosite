<?php

namespace App\Interfaces;

interface Checkout
{
    public function init(): string;

    public function status(): bool;

    public function confirm(): void;
}
