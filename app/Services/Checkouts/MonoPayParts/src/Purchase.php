<?php

namespace App\Services\Checkouts\MonoPayParts\src;

class Purchase
{
    private $config = null;

    private $invoice = null;

    private $products = [];

    public function __construct(Config $config, Invoice $invoice)
    {
        $this->config = $config;
        $this->invoice = $invoice;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function addProduct(string $name, int $qty, float $sum): Purchase
    {
        $this->products[] = [
            'name' => $name,
            'count' => $qty,
            'sum' => $this->formatNumber($sum),
        ];

        return $this;
    }

    public function getRequestString(): string
    {
        return json_encode($this->getRequestArray());
    }

    public function getRequestArray()
    {
        $request = [
            'store_order_id' => $this->getInvoice()->getOrderId(),
            'client_phone' => $this->getInvoice()->getCustomerTelephone(),
            'total_sum' => $this->getTotalSum(),
            'invoice' => [
                'date' => $this->getInvoice()->getDate(),
                'number' => $this->getInvoice()->getNumber(),
                'source' => $this->getInvoice()->getSource(),
            ],
            'available_programs' => $this->getInvoice()->getAvailablePrograms(),
            'products' => $this->products,
            'result_callback' => $this->config->getCallback(),
        ];

        if ($pointId = $this->getInvoice()->getPointId()) {
            $request['invoice']['point_id'] = $pointId;
        }

        return $request;
    }

    protected function getTotalSum(): float
    {
        $sum = 0.0;

        foreach ($this->products as $product) {
            $sum += $product['count'] * $product['sum'];
        }

        return $this->formatNumber($sum);
    }

    protected function formatNumber(float $number): float
    {
        return number_format($number, 2, '.', '');
    }
}
