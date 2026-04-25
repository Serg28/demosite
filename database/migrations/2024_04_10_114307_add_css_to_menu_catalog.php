<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menu_catalog', function (Blueprint $table) {
            $table->string('css_class')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('menu_catalog', function (Blueprint $table) {
            $table->dropColumn('css_class');
        });
    }
};
