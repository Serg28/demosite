<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentInvoice as ModelsPaymentInvoice;

class PaymentInvoice
{

    private Order $order;

    /*
     * @param Order $order Заказ, для которого создается или ищется инвойс.
     *
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Получает или создает инвойс для заказа по указанному методу оплаты.
     *
     * Этот метод сначала проверяет, существует ли инвойс для указанного метода оплаты и заказа.
     * Если инвойс не найден, создается новый с уникальным ID.
     *

     * @param string $paymentMethod Метод оплаты (например, 'monobank', 'liqpay').
     * @return ModelsPaymentInvoice Возвращает существующий или новый инвойс.
     *
     * @example
     * $order = Order::find(1); // Найти заказ с ID 1
     * $invoiceService = new PaymentInvoice($order);
     * $invoice = $invoiceService->getOrCreatePaymentInvoice('monobank');
     * echo $invoice->invoice_id; // Выводит ID инвойса
     */
    public function getOrCreatePaymentInvoice(string $paymentMethod): ModelsPaymentInvoice
    {
        // Проверяем, существует ли инвойс для указанного метода оплаты
        $invoice = $this->order->paymentInvoices()->where('payment_method', $paymentMethod)->latest()->first();

        // Если инвойс не найден, создаем новый
        if (is_null($invoice)) {
            $referenceId = $this->order->id . '_' . time(); // Генерация уникального ID для инвойса
            $invoice = $this->order->paymentInvoices()->create([
                'payment_method' => $paymentMethod, //Алиас платежной системы, напр., liqpay
                'order_reference_id' => $referenceId , //Ид заказа для инвойса
                'payment_id' => $this->order->pay_method_id, // ИД метода оплаты
                'payment_info' => null, // Начальное значение, если необходимо
            ]);
        }

        return $invoice;
    }

    public function getInvoiceById(string $invoiceId): ModelsPaymentInvoice
    {
        return $this->order->paymentInvoices()->where('invoice_id', $invoiceId)->first();
    }

    /**
     * Получает инвойс для заказа по указанному методу оплаты.
     *
     * Этот метод возвращает инвойс для указанного метода оплаты. Если инвойс не найден, возвращает null.
     *
     * @param string $paymentMethod Метод оплаты (например, 'monobank', 'liqpay').
     * @return ModelsPaymentInvoice|null Возвращает инвойс для указанного метода оплаты или null, если не найден.
     *
     * @example
     * $order = Order::find(1); // Найти заказ с ID 1
     * $invoiceService = new PaymentInvoice($order);
     * $invoice = $invoiceService->getPaymentInvoiceByMethod('liqpay');
     * if ($invoice) {
     *     echo $invoice->invoice_id; // Выводит ID инвойса
     * } else {
     *     echo 'Инвойс не найден';
     * }
     */
    public function getPaymentInvoiceByMethod(string $paymentMethod): ?ModelsPaymentInvoice
    {
        return $this->order->paymentInvoices()->where('payment_method', $paymentMethod)->latest()->first();
    }

    /**
     * Получает последний инвойс для заказа.
     *
     * Этот метод возвращает последний инвойс для указанного заказа. Если инвойсы отсутствуют, возвращает null.
     *
     * @return ModelsPaymentInvoice|null Возвращает последний инвойс или null, если инвойсы отсутствуют.
     *
     * @example
     * $order = Order::find(1); // Найти заказ с ID 1
     * $invoiceService = new PaymentInvoice($order);
     * $lastInvoice = $invoiceService->getLastPaymentInvoice();
     * if ($lastInvoice) {
     *     echo $lastInvoice->invoice_id; // Выводит ID последнего инвойса
     * } else {
     *     echo 'Инвойсы не найдены';
     * }
     */
    public function getLastPaymentInvoice(): ?ModelsPaymentInvoice
    {
        return $this->order->paymentInvoices()->latest()->first();
    }

    /**
     * Добавляет новый инвойс для заказа.
     *
     * Этот метод создает новый инвойс для заказа с указанными параметрами.
     *
     * @param string $paymentMethod Метод оплаты (например, 'monobank', 'liqpay').
     * @param string $invoiceId Уникальный ID инвойса.
     * @param string $paymentId Уникальный ID метода оплаты.
     * @param array $paymentInfo Дополнительная информация о платеже (в формате JSON).
     * @return ModelsPaymentInvoice Возвращает созданный инвойс.
     *
     * @example
     * $order = Order::find(1); // Найти заказ с ID 1
     * $invoiceService = new PaymentInvoice($order);
     * $invoice = $invoiceService->addPaymentInvoice('monobank', 'INV123456', 'PAY123456', ['amount' => 1000]);
     * echo $invoice->invoice_id; // Выводит ID созданного инвойса
     */
    public function addPaymentInvoice(string $paymentMethod, string $invoiceId, string $paymentId, array $paymentInfo = []): ModelsPaymentInvoice
    {
        return $this->order->paymentInvoices()->create([
            'payment_method' => $paymentMethod,
            'invoice_id' => $invoiceId,
            'payment_id' => $paymentId,
            'payment_info' => $paymentInfo,
        ]);
    }

    /**
     * Обновляет данные в поле `payment_info` инвойса, добавляя новые данные к уже существующим.
     *
     * Данные хранятся в формате JSON. Если поле `payment_info` пусто или содержит строку в формате JSON,
     * они будут объединены с новыми данными, которые передаются в метод в виде массива.
     * Итоговые данные сохраняются в базу данных.
     *
     * @param ModelsPaymentInvoice $invoice Экземпляр модели `Invoice`, данные которой необходимо обновить.
     * @param array $newPaymentData Массив с новыми данными для добавления в поле `payment_info`.
     *
     * @return void
     *
     * @example Пример использования:
     * ```php
     * $order = Order::find(1); // Найти заказ с ID 1
     * $invoiceService = new PaymentInvoice($order);
     *
     * // Новые данные, которые нужно добавить
     * $newPaymentData = [
     *     'transaction_id' => '1234567890',
     *     'amount' => 1000,
     *     'status' => 'completed',
     * ];
     * $invoice = $invoiceService->getPaymentInvoiceByMethod('monobank')
     * $invoiceService->updatePaymentInfo($invoice, $newPaymentData);
     * ```
     */
    public function updatePaymentInfo(ModelsPaymentInvoice $invoice, array $newPaymentData): void
    {
        // Извлекаем текущие данные из поля payment_info
        $currentPaymentInfo = $invoice->payment_info;

        // Если текущие данные пусты, инициализируем их как пустой массив
        if (empty($currentPaymentInfo)) {
            $currentPaymentInfo = [];
        } elseif (is_string($currentPaymentInfo)) {
            // Если данные хранятся в строковом формате, декодируем их в массив
            $currentPaymentInfo = json_decode($currentPaymentInfo, true);
        }

        // Объединяем новые данные с существующими
        $updatedPaymentInfo = array_merge($currentPaymentInfo, $newPaymentData);

        // Сохраняем обновленные данные обратно в поле payment_info
        $invoice->payment_info = json_encode($updatedPaymentInfo);

        // Сохраняем изменения в базе данных
        $invoice->save();
    }
}
