<?php

namespace App\Interfaces;

interface SortInterface
{
    public function getCountAll(): array;
    public function getSortingAll(): array;
    public function checkSelected(?string $sort, string $value): bool;
    public function urlParams(string $sorting = null, int $countShow = null): array;
    public function getSorting(): string;
    public function setSorting($sorting = null): void;
    public function getCountShow(): int;
    public function setCountShow(string $countShow = null): void;
    public function getDefaultShow(): int;
    public function setDefaultShow(string $defaultShow = null): void;
}
