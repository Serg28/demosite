<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_characteristic_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('feed_id')->index();
            $table->unsignedInteger('characteristic_option_id')->index();

            $table->foreign('characteristic_option_id')->references('id')
                ->on('characteristic_options')->onDelete('cascade');
            $table->foreign('feed_id')->references('id')
                ->on('feeds')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_characteristic_options');
    }
};
