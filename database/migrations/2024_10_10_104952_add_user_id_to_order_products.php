<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->after('order_id')->comment('ID пользователя, который купил товар. Для быстрого поиска купленных пользователем товаров'); // Связь с user_id в таблице orders

            // Установка внешнего ключа на поле user_id
            $table->foreign('user_id')->references('user_id')->on('orders')->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropForeign('order_products_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
};
