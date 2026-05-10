# Вимоги надійності платіжної системи

> Застосовуються до **всіх** гейтвеїв: MonoPay, WayForPay, LiqPay, PrivatParts, EasyPay, NovaPay, Platon, PayLink.  
> Джерело: `yandmi/ТЗ для оплаты моно и заказы/`

---

## Обов'язкові принципи (КРИТИЧНО)

### 1. Замовлення створюється до виклику API банку

Order записується в БД → тільки після цього POST до платіжного гейтвею.  
Якщо банк впав — дані клієнта збережені, менеджер бачить замовлення.

### 2. `payment_url` зберігати в `payment_invoices`

Поле `payment_url` — URL сторінки оплати від банку (`pageUrl` для MonoPay).  
Потрібне для кнопки «Оплатити» в ЛК та «Спробувати ще раз» на сторінці подяки.

### 3. Ідемпотентність платежів

**При ініціалізації:**
```php
// Перед POST до банку — перевірити існуючий інвойс
$invoice = PaymentInvoice::query()
    ->where('order_id', $order->id)
    ->where('pay_method_id', $payMethodId)
    ->whereNotNull('gateway_ref')
    ->whereNotNull('payment_url')
    ->first();

if ($invoice) {
    return $invoice->payment_url; // повернути існуючий URL
}
```

**При записі оплати:**
```php
// Захист від дублювання при race condition webhook + redirect
PaymentInvoice::firstOrCreate(
    ['order_id' => $order->id, 'gateway_ref' => $invoiceId],
    ['status' => 'paid', 'paid_at' => now()]
);
```

### 4. `status()` на сторінці redirectUrl

Webhook може не дійти (мережа, сайт недоступний, хибний URL).  
При поверненні клієнта на сайт **завжди** запитати актуальний статус:

```php
// У CheckoutController::success() або payment result route:
try {
    $gateway->status($order->latestInvoice);
} catch (\Throwable $e) {
    Log::channel('payments')->error('Status check failed', ['order' => $order->id, 'e' => $e->getMessage()]);
}
```

### 5. DB::transaction() при оновленні статусу

```php
DB::transaction(function () use ($order, $invoice, $status) {
    $order->update(['payment_status' => $status]);
    $invoice->update(['status' => $status, 'paid_at' => now(), 'gateway_response' => $response]);
    // createPaymentRecord якщо success
});
```

### 6. Webhook handler повертає HTTP 200 завжди

```php
public function webhook(Request $request, Order $order): Response
{
    try {
        // ... обробка
    } catch (\Throwable $e) {
        Log::channel('payments')->error('Webhook error', ['order' => $order->id, 'e' => $e->getMessage()]);
    }

    return response('', 200); // ЗАВЖДИ 200
}
```

### 7. Перевірка підпису webhook

Перед будь-якою обробкою — верифікація підпису:

| Гейтвей | Підпис |
|---------|--------|
| MonoPay | `X-Sign` (RSA, публічний ключ з API) |
| LiqPay | `data` + `signature` (base64 + SHA1) |
| WayForPay | HMAC-MD5 підпис полів |

Невалідний підпис → `Log::channel('payments')->warning(...)` + `return response('', 200)` (не exception).

### 8. Webhook → черга з retry

```php
// У webhook controller — одразу dispatch у чергу:
ProcessPaymentWebhook::dispatch($order, $payload)->onQueue('payments');

// Job клас:
class ProcessPaymentWebhook implements ShouldQueue
{
    public int $tries = 3;
    public array $backoff = [60, 300, 900]; // 1хв, 5хв, 15хв

    public function handle(GatewayRegistry $registry): void
    {
        $gateway = $registry->resolve($this->order->payMethod->gateway);
        $gateway->confirm($this->order->latestInvoice, $this->payload);
    }
}
```

### 9. Cron-задачі моніторингу

```php
// app/Console/Kernel.php
$schedule->command('payments:check-expired')->hourly();
$schedule->command('payments:check-stuck')->everyFifteenMinutes();
```

**`payments:check-expired`** — замовлення > 7 днів з invoice, статус unpaid → запитати статус у банку.  
**`payments:check-stuck`** — замовлення < 7 днів, з invoice_id, unpaid → запитати статус (webhook міг не дійти).

### 10. Логування в окремий канал

```php
// config/logging.php — додати канал:
'payments' => [
    'driver' => 'daily',
    'path'   => storage_path('logs/payments/payments.log'),
    'level'  => 'debug',
    'days'   => 30,
],
```

Всі запити/відповіді гейтвею → `Log::channel('payments')->info(...)`.

---

## UX вимоги при помилці оплати

- **Клієнт бачить:** текст помилки від банку + номер збереженого замовлення
- **Кнопка «Спробувати ще раз»** → повторний init() з тим самим замовленням (ідемпотентний)
- **Кнопка «Обрати інший спосіб оплати»** → повернення на checkout
- **ЛК (особистий кабінет):** кнопка «Оплатити» біля замовлень зі статусом unpaid + наявним invoice

---

## Проблеми та рішення

| Проблема | Рішення |
|----------|---------|
| Банк впав при створенні інвойсу | Замовлення в БД, note_for_manager, UI retry |
| Webhook не прийшов | `status()` на redirectUrl (п.4) |
| Подвійний клік / повторна оплата | Ідемпотентна перевірка invoice (п.3) |
| Race condition webhook + redirect | `firstOrCreate` для payment record (п.3) |
| Статус expired — без webhook | Cron check-expired (п.9) |
| Часткова запись БД | `DB::transaction()` (п.5) |
| Webhook не оброблено | Retry queue з backoff (п.8) |
| Підроблений webhook | Перевірка підпису (п.7) |

---

## Чеклист для кожного нового гейтвею

- [ ] Замовлення → БД до API
- [ ] `payment_url` зберігається в `payment_invoices`
- [ ] Перевірка існуючого інвойсу перед POST
- [ ] `status()` викликається з redirectUrl
- [ ] `DB::transaction()` при оновленні статусу
- [ ] Webhook handler повертає 200 завжди
- [ ] Перевірка підпису webhook
- [ ] Webhook → queue з `$tries=3`, `$backoff=[60,300,900]`
- [ ] Cron check-expired + check-stuck підтримує цей гейтвей
- [ ] Логування в `payments` channel
