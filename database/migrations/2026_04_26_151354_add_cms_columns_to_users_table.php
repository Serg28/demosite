<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // CMS requires these user fields (from linecore-cms CreateUsersTable migration).
            // Added as nullable because the column may be added to a table that already has rows.
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (! Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('last_name');
            }
            if (! Schema::hasColumn('users', 'permissions')) {
                $table->text('permissions')->nullable()->after('image');
            }
            if (! Schema::hasColumn('users', 'last_login')) {
                $table->dateTime('last_login')->nullable()->after('permissions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter(
                ['first_name', 'last_name', 'image', 'permissions', 'last_login'],
                fn ($col) => Schema::hasColumn('users', $col)
            ));
        });
    }
};
