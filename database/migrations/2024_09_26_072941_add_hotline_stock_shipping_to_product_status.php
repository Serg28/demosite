<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->string('hotline_stock')->nullable()->comment('Сопоставление с полем stock в фиде Hotline');
            $table->string('hotline_shipping')->nullable()->comment('Сопоставление с полем shipping в фиде Hotline');
        });
    }

    public function down(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->dropColumn('hotline_stock');
            $table->dropColumn('hotline_shipping');
        });
    }
};
