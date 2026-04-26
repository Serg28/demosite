<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('translations_phrases_cms')) {
            Schema::create('translations_phrases_cms', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->text('phrase');
            });
        }

        if (! Schema::hasTable('translations_cms')) {
            Schema::create('translations_cms', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->integer('translations_phrases_cms_id')->unsigned();
                $table->string('lang', 2);
                $table->text('translate');
                $table->index('translations_phrases_cms_id');
                $table->foreign('translations_phrases_cms_id')
                    ->references('id')->on('translations_phrases_cms')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations_cms');
        Schema::dropIfExists('translations_phrases_cms');
    }
};
