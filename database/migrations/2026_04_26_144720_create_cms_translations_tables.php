<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the translations tables required by linecore-cms's __t() function.
 * These tables live in vendor migrations but must also exist in test environments
 * where RefreshDatabase only runs database/migrations/.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('translations_phrases')) {
            Schema::create('translations_phrases', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                // utf8_bin matches CMS vendor migration exactly (case-sensitive phrase lookup)
                $table->text('phrase')->collation('utf8_bin');
            });
        }

        if (! Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('id_translations_phrase')->unsigned();
                // lang varchar(2) matches CMS vendor migration ('ua', 'ru', 'en')
                $table->string('lang', 2);
                $table->text('translate');
                $table->index('id_translations_phrase');
                $table->foreign('id_translations_phrase')
                    ->references('id')->on('translations_phrases')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('translations_phrases');
    }
};
