<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('np_warehouse', function (Blueprint $table) {
            $table->tinyInteger('pochtomat')->index()->default(0);
            $table->tinyInteger('is_active')->index()->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('np_warehouse', function (Blueprint $table) {
            $table->dropColumn(['pochtomat','is_active']);
        });
    }
};
