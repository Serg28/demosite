<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feeds', function (Blueprint $table): void {
            $table->unsignedInteger('characteristic_id')->nullable()->index();

            $table->foreign('characteristic_id')->references('id')->on('characteristics');
        });
    }

    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table): void {
            $table->dropColumn(['characteristic_id']);
        });
    }
};
