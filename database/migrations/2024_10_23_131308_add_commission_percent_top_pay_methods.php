<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->decimal('commission_rate', 6, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
