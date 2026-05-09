<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Checkout Phase 4.2: таблиці вже існують у БД (створено вручну).
 * Міграція тільки:
 *  - додає order_status_id до orders
 *  - додає title, count (rename quantity), base_price, amount до order_products
 */
return new class extends Migration
{
    public function up(): void
    {
        // orders — додаємо order_status_id (linecore-demo сумісність)
        if (! Schema::hasColumn('orders', 'order_status_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('order_status_id')->nullable();
                $table->index('order_status_id');
            });
        }

        // order_products — додаємо поля
        Schema::table('order_products', function (Blueprint $table) {
            if (! Schema::hasColumn('order_products', 'title')) {
                $table->string('title')->nullable()->after('order_id');
            }

            if (Schema::hasColumn('order_products', 'quantity')) {
                $table->renameColumn('quantity', 'count');
            }

            if (! Schema::hasColumn('order_products', 'base_price')) {
                $table->decimal('base_price', 12, 2)->nullable()->after('price');
            }

            if (! Schema::hasColumn('order_products', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable()->after('base_price');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'order_status_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['order_status_id']);
                $table->dropColumn('order_status_id');
            });
        }

        Schema::table('order_products', function (Blueprint $table) {
            if (Schema::hasColumn('order_products', 'amount')) {
                $table->dropColumn('amount');
            }

            if (Schema::hasColumn('order_products', 'base_price')) {
                $table->dropColumn('base_price');
            }

            if (Schema::hasColumn('order_products', 'count')) {
                $table->renameColumn('count', 'quantity');
            }

            if (Schema::hasColumn('order_products', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
