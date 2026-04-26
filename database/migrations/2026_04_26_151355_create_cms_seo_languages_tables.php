<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo')) {
            Schema::create('seo', function (Blueprint $table) {
                $table->id();
                $table->integer('seo_id');
                $table->string('seo_type');
                $table->json('seo_h1')->nullable();
                $table->json('seo_title')->nullable();
                $table->json('seo_description')->nullable();
                $table->json('seo_keywords')->nullable();
                $table->json('seo_text')->nullable();
                $table->json('seo_canonical')->nullable();
                $table->tinyInteger('is_seo_noindex');
                $table->tinyInteger('is_seo_nofollow');
                $table->unique(['seo_id', 'seo_type']);
            });
        }

        if (! Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('language', 10);
                $table->tinyInteger('is_active');
                $table->integer('priority')->default(0);
                $table->index(['is_active', 'priority'], 'languages_idx_is_active_priority');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
        Schema::dropIfExists('seo');
    }
};
