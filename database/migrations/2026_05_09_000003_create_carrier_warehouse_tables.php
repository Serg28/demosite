<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.3: таблиці складів/відділень кар'єрів + rozetka_warehouse_id в orders.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── ukrposhta_warehouses ────────────────────────────────────────────────
        Schema::create('ukrposhta_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        // ── justin_warehouses ───────────────────────────────────────────────────
        Schema::create('justin_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        // ── meest_warehouses ────────────────────────────────────────────────────
        Schema::create('meest_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        // ── rozetka_warehouses ──────────────────────────────────────────────────
        Schema::create('rozetka_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        // ── orders: rozetka_warehouse_id ────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'rozetka_warehouse_id')) {
                $table->foreignId('rozetka_warehouse_id')
                    ->nullable()
                    ->constrained('rozetka_warehouses')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'rozetka_warehouse_id')) {
                $table->dropForeign(['rozetka_warehouse_id']);
                $table->dropColumn('rozetka_warehouse_id');
            }
        });

        Schema::dropIfExists('rozetka_warehouses');
        Schema::dropIfExists('meest_warehouses');
        Schema::dropIfExists('justin_warehouses');
        Schema::dropIfExists('ukrposhta_warehouses');

        Schema::enableForeignKeyConstraints();
    }
};
