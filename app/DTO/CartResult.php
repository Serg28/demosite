<?php

namespace App\DTO;

readonly class CartResult
{
    public function __construct(
        public bool $status,
        public string $message,
        public int $count,
        public string $action,
        public array $products = [],
    ) {}

    public static function success(string $message, int $count, string $action, array $products = []): self
    {
        return new self(true, $message, $count, $action, $products);
    }

    public static function error(string $message): self
    {
        return new self(false, $message, 0, 'error');
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'count' => $this->count,
            'action' => $this->action,
            'products' => $this->products,
        ];
    }
}
