<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // [{"min_qty": 10, "rate": 0.95}, {"min_qty": 50, "rate": 0.90}]
            $table->json('wholesale_tiers')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('wholesale_tiers');
        });
    }
};
