<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 4.3b: meta JSON для carrier-specific полів складів.
 * НП: only_receiving_parcel, max_weight
 * Rozetka: can_give_out_tracks, can_receive_tracks, volume_weight, schedule
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_warehouses', function (Blueprint $table) {
            $table->json('meta')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_warehouses', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
