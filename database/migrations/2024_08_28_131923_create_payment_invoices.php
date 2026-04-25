<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->id();  // Уникальный ID записи в таблице
            $table->foreignId('order_id')->constrained()->onDelete('cascade');  // Связь с заказом
            $table->string('invoice_id')->index()->nullable()->default(null);  // Уникальный идентификатор инвойса в платежной системе
            $table->string('order_reference_id')->index();  // Уникальный идентификатор заказа для инвойса
            $table->string('payment_id')->index();  // Уникальный идентификатор метода оплаты
            $table->string('payment_method')->nullable()->default(null);  // Метод оплаты (например, 'monobank', 'liqpay')
            $table->json('payment_info')->nullable();  // JSON с ответом от сервера для данного инвойса
            $table->timestamps();  // Временные метки создания и обновления
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_invoices');
    }
};
