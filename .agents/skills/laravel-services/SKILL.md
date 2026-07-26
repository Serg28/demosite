---
name: laravel-services
description: Service layer for external API integration using manager pattern and Http::-facade HTTP clients. Use when integrating external APIs or third-party services.
---

# Laravel Services

External services use Laravel's **Manager pattern** with multiple drivers.

HTTP calls go through a dedicated `*Client` class using `Illuminate\Support\Facades\Http` —
this is the project's existing, working convention (see `app/Services/Payment/Gateways/*/*.php`,
`app/Services/Sms/Drivers/*.php`). **Not Saloon** — see [Packages](../laravel-packages/SKILL.md#not-used-in-this-project)
for why a second HTTP client style isn't introduced.

**Related guides:**
- [Actions](../laravel-actions/SKILL.md) - Actions use services
- [Testing](../laravel-testing/SKILL.md) - Testing with null drivers

## When to Use

**Use service layer when:**
- Integrating external APIs
- Multiple drivers for same service (email, payment, SMS)
- Need to swap implementations
- Want null driver for testing

## Structure

```
Services/
└── Payment/
    ├── PaymentManager.php          # Manager (extends Laravel Manager)
    ├── Gateways/
    │   └── Stripe/
    │       └── StripeClient.php    # Http::-facade HTTP client
    ├── Contracts/
    │   └── PaymentDriver.php       # Driver interface
    ├── Drivers/
    │   ├── StripeDriver.php        # Stripe implementation
    │   ├── PayPalDriver.php        # PayPal implementation
    │   └── NullDriver.php          # For testing
    ├── Exceptions/
    │   └── PaymentException.php
    └── Facades/
        └── Payment.php             # Facade
```

## Manager Class

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Drivers\NullDriver;
use App\Services\Payment\Drivers\StripeDriver;
use Illuminate\Support\Manager;

class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('payment.default');
    }

    public function createStripeDriver(): StripeDriver
    {
        return new StripeDriver(
            client: new StripeClient(
                apiKey: $this->config->get('payment.drivers.stripe.api_key'),
            ),
            webhookSecret: $this->config->get('payment.drivers.stripe.webhook_secret'),
        );
    }

    public function createNullDriver(): NullDriver
    {
        return new NullDriver;
    }
}
```

## Driver Contract

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment\Contracts;

use App\Data\PaymentIntentData;

interface PaymentDriver
{
    public function createPaymentIntent(int $amount, string $currency): PaymentIntentData;

    public function refundPayment(string $paymentIntentId, ?int $amount = null): bool;

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntentData;
}
```

## Driver Implementation

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Data\PaymentIntentData;
use App\Services\Payment\Gateways\Stripe\StripeClient;
use App\Services\Payment\Contracts\PaymentDriver;

class StripeDriver implements PaymentDriver
{
    public function __construct(
        private readonly StripeClient $client,
        private readonly string $webhookSecret,
    ) {}

    public function createPaymentIntent(int $amount, string $currency): PaymentIntentData
    {
        $response = $this->client->createPaymentIntent($amount, $currency);

        return PaymentIntentData::from($response);
    }

    public function refundPayment(string $paymentIntentId, ?int $amount = null): bool
    {
        // Implementation...
    }
}
```

## HTTP Client

One `*Client` class per external gateway, using `Illuminate\Support\Facades\Http` — same shape as
`app/Services/Payment/Gateways/LiqPay/LiqPayClient.php` and the other existing gateway clients:

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways\Stripe;

use App\Services\Payment\Exceptions\PaymentException;
use Illuminate\Support\Facades\Http;

class StripeClient
{
    public function __construct(private readonly string $apiKey) {}

    public function createPaymentIntent(int $amount, string $currency): array
    {
        $response = Http::baseUrl('https://api.stripe.com')
            ->withToken($this->apiKey)
            ->asJson()
            ->post('/v1/payment_intents', [
                'amount' => $amount,
                'currency' => $currency,
            ]);

        if ($response->failed()) {
            throw PaymentException::failedRequest($response);
        }

        return $response->json();
    }
}
```

## Facade

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment\Facades;

use App\Data\PaymentIntentData;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PaymentIntentData createPaymentIntent(int $amount, string $currency)
 * @method static bool refundPayment(string $paymentIntentId, ?int $amount = null)
 * @method static PaymentIntentData retrievePaymentIntent(string $paymentIntentId)
 *
 * @see \App\Services\Payment\PaymentManager
 */
class Payment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\Payment\PaymentManager::class;
    }
}
```

## Usage

```php
use App\Services\Payment\Facades\Payment;

// Use default driver
$paymentIntent = Payment::createPaymentIntent(
    amount: 10000,  // $100.00 in cents
    currency: 'usd'
);

// Refund payment
Payment::refundPayment($paymentIntent->id);

// Use specific driver
Payment::driver('stripe')->createPaymentIntent(10000, 'usd');
Payment::driver('paypal')->createPaymentIntent(10000, 'usd');

// Use in actions
class ProcessPaymentAction
{
    public function __invoke(Order $order, PaymentData $data): Payment
    {
        $paymentIntent = Payment::createPaymentIntent(
            amount: $order->total,
            currency: 'usd'
        );

        // ...
    }
}
```

## Null Driver for Testing

```php
<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Data\PaymentIntentData;
use App\Services\Payment\Contracts\PaymentDriver;

class NullDriver implements PaymentDriver
{
    public function createPaymentIntent(int $amount, string $currency): PaymentIntentData
    {
        return PaymentIntentData::from([
            'id' => 'pi_test_' . uniqid(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'succeeded',
        ]);
    }

    public function refundPayment(string $paymentIntentId, ?int $amount = null): bool
    {
        return true;
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntentData
    {
        return PaymentIntentData::from([
            'id' => $paymentIntentId,
            'status' => 'succeeded',
        ]);
    }
}
```

