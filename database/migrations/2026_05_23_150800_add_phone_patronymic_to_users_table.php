<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('last_name');
            }

            if (! Schema::hasColumn('users', 'patronymic')) {
                $table->string('patronymic', 60)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'patronymic']);
        });
    }
};
