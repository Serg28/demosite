<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.9: додаємо receiver поля + call_me до orders,
 * виправляємо відсутній is_active у gift_certificates (баг: є в Model casts але не в таблиці).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'receiver_first_name')) {
                $table->string('receiver_first_name')->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('orders', 'receiver_last_name')) {
                $table->string('receiver_last_name')->nullable()->after('receiver_first_name');
            }
            if (! Schema::hasColumn('orders', 'receiver_patronymic')) {
                $table->string('receiver_patronymic')->nullable()->after('receiver_last_name');
            }
            if (! Schema::hasColumn('orders', 'receiver_phone')) {
                $table->string('receiver_phone')->nullable()->after('receiver_patronymic');
            }
            if (! Schema::hasColumn('orders', 'receiver_email')) {
                $table->string('receiver_email')->nullable()->after('receiver_phone');
            }
            if (! Schema::hasColumn('orders', 'call_me')) {
                $table->boolean('call_me')->default(false)->after('comment');
            }
        });

        Schema::table('gift_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('gift_certificates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['receiver_first_name', 'receiver_last_name', 'receiver_patronymic', 'receiver_phone', 'receiver_email', 'call_me'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('gift_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('gift_certificates', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
