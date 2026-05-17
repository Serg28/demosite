<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.9 J+K: розширення discount_cards (barcode, phone-lookup, user) та promo_codes (usage_type, limits, compatibility flags).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discount_cards', function (Blueprint $table) {
            $table->string('barcode', 64)->nullable()->after('code');
            $table->string('name', 128)->nullable()->after('barcode');
            $table->string('phone', 32)->nullable()->after('name');
            $table->string('email', 128)->nullable()->after('phone');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('email');
            $table->string('address')->nullable()->after('user_id');
            $table->string('city', 128)->nullable()->after('address');
            $table->text('comment')->nullable()->after('city');
            $table->boolean('use_for_installments')->default(true)->after('is_active');
            $table->boolean('use_for_promotional')->default(true)->after('use_for_installments');

            $table->index('phone');
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->enum('usage_type', ['once', 'reusable'])->default('reusable')->after('is_active');
            $table->boolean('is_used')->default(false)->after('usage_type');
            $table->unsignedInteger('max_uses')->nullable()->after('is_used');
            $table->unsignedInteger('used_count')->default(0)->after('max_uses');
            $table->boolean('use_for_installments')->default(true)->after('used_count');
            $table->boolean('use_for_promotional')->default(true)->after('use_for_installments');
            $table->boolean('use_for_discount_cards')->default(true)->after('use_for_promotional');
        });
    }

    public function down(): void
    {
        Schema::table('discount_cards', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'barcode', 'name', 'phone', 'email', 'user_id',
                'address', 'city', 'comment',
                'use_for_installments', 'use_for_promotional',
            ]);
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn([
                'usage_type', 'is_used', 'max_uses', 'used_count',
                'use_for_installments', 'use_for_promotional', 'use_for_discount_cards',
            ]);
        });
    }
};
