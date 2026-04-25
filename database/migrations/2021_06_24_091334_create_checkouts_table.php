<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutsTable extends Migration
{
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title', 256)->nullable();
            $table->string('title_ru', 256)->nullable();
            $table->string('title_en', 256)->nullable();
            $table->string('slug', 256)->nullable();
            $table->text('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0);
            $table->string('picture', 256)->nullable();
            $table->string('picture_ru', 256)->nullable();
            $table->string('picture_en', 256)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
}
