<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->string('schema_availability')->nullable()->comment('Значение поля availability в разметке Schema.org');
        });
    }

    public function down(): void
    {
        Schema::table('product_status', function (Blueprint $table) {
            $table->dropColumn('schema_availability');
        });
    }
};
