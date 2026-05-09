<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.3: таблиці для checkout (доставка, оплата, промокоди, міста, склади тощо)
 * + нові колонки в orders для повної checkout-архітектури.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── deliveries ──────────────────────────────────────────────────────────
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('free_cost', 10, 2)->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
        });

        // ── pay_methods ─────────────────────────────────────────────────────────
        Schema::create('pay_methods', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->string('gateway')->default('');
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
        });

        // ── delivery_payment (pivot) ────────────────────────────────────────────
        Schema::create('delivery_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('pay_methods')->cascadeOnDelete();
            $table->unique(['delivery_id', 'payment_id']);
        });

        // ── promo_codes ─────────────────────────────────────────────────────────
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── discount_cards ──────────────────────────────────────────────────────
        Schema::create('discount_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── cities ──────────────────────────────────────────────────────────────
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('is_active')->default(true);
        });

        // ── np_warehouses ───────────────────────────────────────────────────────
        Schema::create('np_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->index('city_id');
        });

        // ── delivery_pickup_points ──────────────────────────────────────────────
        Schema::create('delivery_pickup_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->json('title');
            $table->string('address')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
        });

        // ── payment_credentials ─────────────────────────────────────────────────
        Schema::create('payment_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_method_id')->constrained('pay_methods')->cascadeOnDelete();
            $table->tinyInteger('day_of_week')->nullable();
            $table->json('credentials');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // ── payment_invoices ────────────────────────────────────────────────────
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('pay_method_id')->nullable()->constrained('pay_methods')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status', 32)->default('pending');
            $table->string('gateway_ref')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });

        // ── gift_certificates ───────────────────────────────────────────────────
        Schema::create('gift_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_used')->default(false);
            $table->foreignId('used_in_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('use_for_discount_cards')->default(true);
            $table->boolean('use_for_promotional')->default(true);
            $table->boolean('use_for_installments')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // ── orders: нові колонки ────────────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('orders', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('last_name');
            }

            if (! Schema::hasColumn('orders', 'delivery_id')) {
                $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'pay_method_id')) {
                $table->foreignId('pay_method_id')->nullable()->constrained('pay_methods')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'city_id')) {
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'np_warehouse_id')) {
                $table->foreignId('np_warehouse_id')->nullable()->constrained('np_warehouses')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'ukrposhta_warehouse_id')) {
                $table->unsignedBigInteger('ukrposhta_warehouse_id')->nullable();
            }

            if (! Schema::hasColumn('orders', 'justin_warehouse_id')) {
                $table->unsignedBigInteger('justin_warehouse_id')->nullable();
            }

            if (! Schema::hasColumn('orders', 'meest_warehouse_id')) {
                $table->unsignedBigInteger('meest_warehouse_id')->nullable();
            }

            if (! Schema::hasColumn('orders', 'delivery_pickup_point_id')) {
                $table->foreignId('delivery_pickup_point_id')
                    ->nullable()
                    ->constrained('delivery_pickup_points')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'cost_without_sale')) {
                $table->decimal('cost_without_sale', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('orders', 'price_delivery')) {
                $table->decimal('price_delivery', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('orders', 'sale')) {
                $table->decimal('sale', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('orders', 'sale_promo')) {
                $table->decimal('sale_promo', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('orders', 'sale_discount')) {
                $table->decimal('sale_discount', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('orders', 'payment_commission')) {
                $table->decimal('payment_commission', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('orders', function (Blueprint $table) {
            $fks = ['user_id', 'delivery_id', 'pay_method_id', 'city_id',
                'np_warehouse_id', 'delivery_pickup_point_id'];

            foreach ($fks as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropForeign([$col]);
                    $table->dropColumn($col);
                }
            }

            $plain = ['first_name', 'last_name', 'ukrposhta_warehouse_id',
                'justin_warehouse_id', 'meest_warehouse_id', 'cost_without_sale',
                'price_delivery', 'sale', 'sale_promo', 'sale_discount', 'payment_commission'];

            foreach ($plain as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('gift_certificates');
        Schema::dropIfExists('payment_invoices');
        Schema::dropIfExists('payment_credentials');
        Schema::dropIfExists('delivery_pickup_points');
        Schema::dropIfExists('np_warehouses');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('discount_cards');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('delivery_payment');
        Schema::dropIfExists('pay_methods');
        Schema::dropIfExists('deliveries');

        Schema::enableForeignKeyConstraints();
    }
};
