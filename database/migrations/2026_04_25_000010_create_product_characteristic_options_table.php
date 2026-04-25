<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_characteristic_options')) {
            Schema::create('product_characteristic_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('characteristic_option_id')->constrained('characteristic_options')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'characteristic_option_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_characteristic_options');
    }
};
