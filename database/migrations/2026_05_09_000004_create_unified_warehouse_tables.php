<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.3: уніфікована таблиця складів/відділень + city_carrier_codes для 1С.
 * Замінює 5 окремих таблиць (np_warehouses, ukrposhta_warehouses, ...).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── delivery_warehouses ─────────────────────────────────────────────────
        Schema::create('delivery_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('carrier', 32);              // np | ukrposhta | justin | meest | rozetka
            $table->string('type', 32)->nullable();     // branch | poshtamat | pvz | null
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('carrier_ref', 64);          // оригінальний ID від кар'єра (UUID для НП, int для інших) — для 1С
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unique(['carrier', 'carrier_ref']);
            $table->index(['carrier', 'city_id']);
            $table->index(['carrier', 'type', 'city_id']);
        });

        // ── city_carrier_codes ──────────────────────────────────────────────────
        Schema::create('city_carrier_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('carrier', 32);              // np | ukrposhta | justin | meest | rozetka
            $table->string('ref', 64);                  // оригінальний ID міста від кар'єра — для 1С
            $table->timestamps();

            $table->unique(['carrier', 'ref']);
            $table->index(['city_id', 'carrier']);
        });

        // ── orders: замінюємо 5 FK-колонок на delivery_warehouse_id ────────────
        Schema::table('orders', function (Blueprint $table) {
            // спочатку прибираємо foreign keys (якщо є)
            if (Schema::hasColumn('orders', 'np_warehouse_id')) {
                $table->dropForeign(['np_warehouse_id']);
                $table->dropColumn('np_warehouse_id');
            }
            if (Schema::hasColumn('orders', 'rozetka_warehouse_id')) {
                $table->dropForeign(['rozetka_warehouse_id']);
                $table->dropColumn('rozetka_warehouse_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            foreach (['ukrposhta_warehouse_id', 'justin_warehouse_id', 'meest_warehouse_id'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_warehouse_id')
                ->nullable()
                ->after('city_id')
                ->constrained('delivery_warehouses')
                ->nullOnDelete();
        });

        // ── drop per-carrier tables ─────────────────────────────────────────────
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rozetka_warehouses');
        Schema::dropIfExists('meest_warehouses');
        Schema::dropIfExists('justin_warehouses');
        Schema::dropIfExists('ukrposhta_warehouses');
        Schema::dropIfExists('np_warehouses');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // відновлюємо per-carrier таблиці
        Schema::create('np_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        Schema::create('ukrposhta_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        Schema::create('justin_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        Schema::create('meest_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        Schema::create('rozetka_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_warehouse_id')) {
                $table->dropForeign(['delivery_warehouse_id']);
                $table->dropColumn('delivery_warehouse_id');
            }
            $table->foreignId('np_warehouse_id')->nullable()->constrained('np_warehouses')->nullOnDelete();
            $table->unsignedBigInteger('ukrposhta_warehouse_id')->nullable();
            $table->unsignedBigInteger('justin_warehouse_id')->nullable();
            $table->unsignedBigInteger('meest_warehouse_id')->nullable();
            $table->foreignId('rozetka_warehouse_id')->nullable()->constrained('rozetka_warehouses')->nullOnDelete();
        });

        Schema::dropIfExists('city_carrier_codes');
        Schema::dropIfExists('delivery_warehouses');

        Schema::enableForeignKeyConstraints();
    }
};
