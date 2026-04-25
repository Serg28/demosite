<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('legal_entities_recipient_id')->index()->nullable();
            $table->string('type');
            $table->integer('price');
            $table->integer('priority');
            $table->tinyInteger('is_payed');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('legal_entities_recipient_id')->references('id')->on('legal_entities_recipients');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
}
