<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_category', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('feed_id')->index();
            $table->unsignedBigInteger('category_id')->index();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('feed_id')->references('id')->on('feeds')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_category');
    }
};
