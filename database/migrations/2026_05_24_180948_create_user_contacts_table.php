<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['address', 'recipient'])->index();
            $table->string('label')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone', 20)->nullable()->index();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->json('info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_contacts');
    }
};
