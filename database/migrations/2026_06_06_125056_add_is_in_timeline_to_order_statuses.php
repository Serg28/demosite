<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->boolean('is_in_timeline')->default(false)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            $table->dropColumn('is_in_timeline');
        });
    }
};
