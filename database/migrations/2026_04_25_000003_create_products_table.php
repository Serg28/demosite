<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('code')->nullable();
            $table->json('description');
            $table->json('short_description')->nullable();
            $table->decimal('price', 10, 2)->index();
            $table->decimal('price_old', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->string('picture')->nullable();
            $table->json('other_pictures')->nullable();
            $table->json('link_to_youtube')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique()->index();
            $table->smallInteger('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
