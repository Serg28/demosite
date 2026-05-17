<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->boolean('is_for_all_cities')->default(true)->after('free_cost');
        });

        Schema::create('city_delivery', function (Blueprint $table) {
            $table->foreignId('delivery_id')->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->primary(['delivery_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_delivery');

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('is_for_all_cities');
        });
    }
};
