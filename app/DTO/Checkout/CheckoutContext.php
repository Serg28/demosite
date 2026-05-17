<?php

namespace App\DTO\Checkout;

use App\Contracts\DiscountSource;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\PayMethod;
use Illuminate\Support\Collection;

/**
 * Shared state, що передається через всі кроки CheckoutPipeline.
 * Мутабельний — кожен крок наповнює потрібні поля.
 */
class CheckoutContext
{
    // Cart
    public Collection $cartItems;
    public float $subtotal = 0.0;

    // Discounts
    /** @var DiscountSource[] */
    public array $appliedDiscounts = [];
    public DiscountBreakdown $discountBreakdown;
    public float $discountTotal = 0.0;

    // Gift Certificates
    /** @var string[] */
    public array $giftCertificateCodes = [];
    public float $giftCertificateTotal = 0.0;

    // Delivery
    public ?Delivery $delivery = null;
    public float $deliveryPrice = 0.0;

    // Taxes & Commissions
    public TaxBreakdown $taxes;
    public string $taxMode;
    public string $discountMode;

    // Payment
    public ?PayMethod $payMethod = null;
    public float $total = 0.0;
    public float $paymentAmount = 0.0;  // total - giftCertificateTotal → іде в gateway

    // Customer
    public string $firstName = '';
    public string $lastName = '';
    public string $phone = '';
    public string $email = '';
    public string $comment = '';
    public ?int $userId = null;
    public bool $callMe = false;

    // Receiver (другий одержувач)
    public string $receiver = 'user';
    public string $receiverFirstName = '';
    public string $receiverLastName = '';
    public string $receiverPatronymic = '';
    public string $receiverPhone = '';
    public string $receiverEmail = '';

    // Delivery details
    public ?int $cityId = null;
    public ?int $deliveryWarehouseId = null;   // unified: delivery_warehouses.id
    public ?int $deliveryPickupPointId = null;
    public string $address = '';

    // Payment sub-form fields
    public ?int $payPartsCount = null;         // монтяків розстрочки
    public string $b2bCompany = '';            // paylink: назва юрособи
    public string $b2bEdrpou = '';             // paylink: ЄДРПОУ

    // Result
    public ?Order $order = null;
    public bool $isPaidByCertificates = false;
    public bool $failed = false;
    public ?string $failReason = null;

    // Pipeline error handling
    public ?string $paymentRedirectUrl = null;

    public function __construct()
    {
        $this->cartItems = collect();
        $this->discountBreakdown = DiscountBreakdown::empty();
        $this->taxes = TaxBreakdown::empty();
        $this->taxMode = config('checkout.tax_mode', 'per_product');
        $this->discountMode = config('checkout.discount_mode', 'per_product');
    }

    public function fail(string $reason): void
    {
        $this->failed = true;
        $this->failReason = $reason;
    }

    public function hasFailed(): bool
    {
        return $this->failed;
    }
}
