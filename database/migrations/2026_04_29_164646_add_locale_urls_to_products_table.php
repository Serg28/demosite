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
        Schema::table('products', function (Blueprint $table) {
            foreach (config('site.title_locales', ['ua', 'ru', 'en']) as $locale) {
                $table->string("{$locale}_url")->nullable()->unique()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (config('site.title_locales', ['ua', 'ru', 'en']) as $locale) {
                $table->dropColumn("{$locale}_url");
            }
        });
    }
};
