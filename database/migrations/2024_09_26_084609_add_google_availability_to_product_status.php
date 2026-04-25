<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->string('google_availability')->nullable()->comment('Значение поля availability в фиде Google');
        });
    }

    public function down(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->dropColumn('google_availability');
        });
    }
};
