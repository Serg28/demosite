<?php

namespace App\Services\Checkouts\MonoPayParts\src;

class Invoice
{
    const INVOICE_SOURCE_STORE = 'STORE';

    const INVOICE_SOURCE_INTERNET = 'INTERNET';

    const INVOICE_PROGRAM_TYPE_INSTALLMENTS = 'payment_installments';

    const INVOICE_DATE_FORMAT = 'Y-m-d';

    private $orderId = null;

    private $customerTelephone = null;

    private $date = null;

    private $availablePartsCount = null;

    private $number = null;

    private $pointId = null;

    private $source = null;

    private $type = null;

    public function __construct(string $orderId, string $customerTelephone, string $number, array $availablePartsCount)
    {
        $this->orderId = $orderId;
        $this->customerTelephone = $customerTelephone;
        $this->number = $number;
        $this->availablePartsCount = $availablePartsCount;
        $this->source = self::INVOICE_SOURCE_INTERNET;
        $this->type = self::INVOICE_PROGRAM_TYPE_INSTALLMENTS;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getCustomerTelephone(): string
    {
        return $this->customerTelephone;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getAvailablePartsCount(): array
    {
        return $this->availablePartsCount;
    }

    public function getAvailablePrograms(): array
    {
        return [
            [
                'available_parts_count' => $this->getAvailablePartsCount(),
                'type' => $this->getType(),
            ],
        ];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Invoice
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date ?? date(self::INVOICE_DATE_FORMAT);
    }

    public function setDate(string $date): Invoice
    {
        $this->date = $date;

        return $this;
    }

    public function getPointId(): ?string
    {
        return $this->pointId;
    }

    public function setPointId(string $pointId): Invoice
    {
        $this->pointId = $pointId;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): Invoice
    {
        $this->source = $source;

        return $this;
    }
}
