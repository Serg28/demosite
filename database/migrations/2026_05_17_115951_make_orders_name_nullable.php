<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Поле name залишилось від початкового create_orders і не заповнюється в CreateOrder.
 * first_name / last_name — актуальні поля. name → nullable для сумісності.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }
};
