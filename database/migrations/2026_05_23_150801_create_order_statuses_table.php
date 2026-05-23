<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table): void {
            $table->id();
            $table->json('title');
            $table->string('color', 20)->nullable();
            $table->integer('priority')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
