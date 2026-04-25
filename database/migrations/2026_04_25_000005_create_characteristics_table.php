<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('characteristics')) {
            Schema::create('characteristics', function (Blueprint $table) {
                $table->id();
                $table->json('title');
                $table->string('slug')->unique()->nullable();
                $table->boolean('is_range_type')->default(false)->comment('Включить слайдер диапазона');
                $table->boolean('is_active')->default(true);
                $table->integer('priority')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
