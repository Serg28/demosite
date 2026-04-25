<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->string('id_1c', 36)->after('id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn(['id_1c']);
        });
    }
};
