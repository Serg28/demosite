<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('category_characteristic')) {
            Schema::create('category_characteristic', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->foreignId('characteristic_id')->constrained('characteristics')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['category_id', 'characteristic_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_characteristic');
    }
};
