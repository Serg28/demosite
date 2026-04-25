<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->nullable()->index();
            $table->smallInteger('lft')->index();
            $table->smallInteger('rgt')->index();
            $table->tinyInteger('depth')->default(0);
            $table->json('title')->nullable();
            $table->string('slug')->unique()->index();
            $table->string('picture')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
