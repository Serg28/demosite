# Checkout — Payment Webhooks (Phase 4.6)

> Статус: реалізовано | Сесія 36

## Що є

- Єдина точка входу для webhook-ів від усіх платіжних систем
- Асинхронна обробка через Job (HTTP 200 повертається завжди, негайно)
- Оновлення статусу замовлення + PaymentInvoice
- Спеціальний обробник для PayLink (потребує додаткового запиту getTranState)

## Структура файлів

```
app/
├── Http/Controllers/PaymentWebhookController.php
│   ├── handle(Request, string $gateway)      # загальний webhook
│   └── handlePayLink(Request)                # PayLink: sid → getTranState
├── Jobs/ProcessPaymentWebhook.php            # ShouldQueue: викликає WebhookProcessor
├── Services/Payment/WebhookProcessor.php     # process(gateway, payload) → оновлює Order
```

## Маршрути

```php
// routes/web.php — без CSRF, без auth
POST /payment/webhook/paylink    → PaymentWebhookController::handlePayLink  (name: payment.webhook.paylink)
POST /payment/webhook/{gateway}  → PaymentWebhookController::handle          (name: payment.webhook)
```

Виключено з CSRF через `VerifyCsrfToken` middleware.

## Потік обробки

```
Платіжна система → POST /payment/webhook/{gateway}
    ↓
PaymentWebhookController::handle()
    ↓ validates gateway exists in registry
ProcessPaymentWebhook::dispatch(gateway, payload)  ← Job у черзі
    ↓ (async, відповідь HTTP 200 вже відправлена)
WebhookProcessor::process()
    ↓
GatewayRegistry → gateway->status(payload)
    ↓
PaymentInvoice::updateStatus()  +  Order::updateStatus()
```

## PaymentWebhookController

```php
// Загальний обробник
public function handle(Request $request, string $gateway): Response
// 1. Перевіряє чи є gateway в registry
// 2. Диспатчить ProcessPaymentWebhook Job
// 3. Завжди повертає response('', 200)

// PayLink-специфічний
public function handlePayLink(Request $request): Response
// 1. Зчитує sid з request
// 2. Викликає PayLinkClient::getTranState(sid)
// 3. Збагачує payload: {order_id=TRANID, resultCode}
// 4. Диспатчить ProcessPaymentWebhook Job
```

## ProcessPaymentWebhook Job

```php
class ProcessPaymentWebhook implements ShouldQueue
{
    public function handle(WebhookProcessor $processor): void
    {
        $result = $processor->process($this->gatewaySlug, $this->payload);
        // Логує результат у Log::channel('payments')
    }
}
```

## WebhookProcessor

Делегує до конкретного гейтвею:
```php
gateway->status($payload)
// Повертає: 'paid' | 'failed' | 'pending' | 'cancelled'
```

Оновлює:
- `PaymentInvoice` → статус, відповідь від шлюзу
- `Order` → статус (paid/failed/cancelled)

## Логування

Всі webhook-и логуються в `Log::channel('payments')`:
- `payments/payments.log` — всі events
- Невідомий gateway → `warning`
- Помилка диспатчу → `error`

---

## Для адміністратора

### Як налаштувати URL для платіжної системи

URL-и для webhook-ів формуються за паттерном:

```
https://yourdomain.com/payment/webhook/{gateway}
```

Наприклад:
- LiqPay: `https://yourdomain.com/payment/webhook/liqpay`
- Monobank: `https://yourdomain.com/payment/webhook/monopay`
- PayLink: `https://yourdomain.com/payment/webhook/paylink` ← спеціальний endpoint

Вказати ці URL у налаштуваннях відповідної платіжної системи.

### Що перевірити якщо webhook не працює

1. **Логи**: `storage/logs/payments/payments.log`
2. **Черга**: переконатись що Horizon запущено (`php artisan horizon`)
3. **CSRF**: маршрут має бути у винятках `VerifyCsrfToken`
4. **Gateway slug**: slug у URL має збігатись з ключем у `config('payment.gateways')`

### Як додати новий gateway

```php
// config/payment.php
'gateways' => [
    'mygateway' => MyGatewayGateway::class,
],
```

URL webhook-а автоматично: `/payment/webhook/mygateway`

Клас `MyGatewayGateway` має реалізувати `PaymentGatewayInterface` з методом `status($payload)`.
