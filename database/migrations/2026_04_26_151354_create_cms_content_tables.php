<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->string('type');
                $table->string('title');
                $table->string('slug')->index();
                $table->string('value')->nullable();
                $table->string('group');
                $table->json('value_languages')->nullable();
                $table->string('file')->nullable();
                $table->tinyInteger('check')->default(0);
                $table->longText('textarea')->nullable();
                $table->json('textarea_with_languages')->nullable();
                $table->json('froala_with_languages')->nullable();
            });
        }

        if (! Schema::hasTable('tb_tree')) {
            Schema::create('tb_tree', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->unsignedInteger('parent_id')->nullable();
                $table->integer('lft');
                $table->integer('rgt');
                $table->integer('depth');
                $table->json('title')->nullable();
                $table->json('description')->nullable();
                $table->string('slug')->index();
                $table->json('url')->nullable();
                $table->string('template', 120);
                $table->string('picture')->nullable();
                $table->string('ico')->nullable();
                $table->string('css', 30)->nullable();
                $table->tinyInteger('is_active');
                $table->timestamps();
                $table->index('lft');
                $table->index('rgt');
                $table->foreign('parent_id')->references('id')->on('tb_tree')->onDelete('cascade');
            });

            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('ALTER TABLE tb_tree ADD UNIQUE INDEX tb_tree_url_unique (url(100))');

                Schema::table('tb_tree', function (Blueprint $table) {
                    foreach (['ru', 'ua', 'en', 'pl'] as $lang) {
                        $table->string("{$lang}_url")->virtualAs("JSON_UNQUOTE(JSON_EXTRACT(url, '$.\"$lang\"'))")->nullable();
                        $table->index("{$lang}_url");
                    }
                });

                DB::statement('
                    CREATE TRIGGER before_tb_tree_insert
                    BEFORE INSERT ON tb_tree FOR EACH ROW
                    BEGIN
                        IF NEW.url = \'{"ua":"","ru":"","en":"","pl":""}\' THEN
                            SET NEW.url = NULL;
                        END IF;
                    END
                ');
                DB::statement('
                    CREATE TRIGGER before_tb_tree_update
                    BEFORE UPDATE ON tb_tree FOR EACH ROW
                    BEGIN
                        IF NEW.url = \'{"ua":"","ru":"","en":"","pl":""}\' THEN
                            SET NEW.url = NULL;
                        END IF;
                    END
                ');

                foreach (['ru', 'ua', 'en', 'pl'] as $lang) {
                    DB::unprepared("
                        CREATE TRIGGER before_tb_tree_{$lang}_url_insert BEFORE INSERT ON tb_tree
                        FOR EACH ROW BEGIN
                            SET NEW.{$lang}_url = JSON_UNQUOTE(JSON_EXTRACT(NEW.url, '$.\"$lang\"'));
                        END
                    ");
                    DB::unprepared("
                        CREATE TRIGGER before_tb_tree_{$lang}_url_update BEFORE UPDATE ON tb_tree
                        FOR EACH ROW BEGIN
                            SET NEW.{$lang}_url = JSON_UNQUOTE(JSON_EXTRACT(NEW.url, '$.\"$lang\"'));
                        END
                    ");
                }
            }
        }

        if (! Schema::hasTable('revisions')) {
            Schema::create('revisions', function (Blueprint $table) {
                $table->charset = 'utf8';
                $table->collation = 'utf8_general_ci';
                $table->increments('id');
                $table->string('revisionable_type', 100);
                $table->integer('revisionable_id');
                $table->integer('user_id')->nullable();
                $table->string('key')->nullable();
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->timestamps();
                $table->index(['revisionable_id', 'revisionable_type']);
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            foreach (['ru', 'ua', 'en', 'pl'] as $lang) {
                DB::unprepared("DROP TRIGGER IF EXISTS before_tb_tree_{$lang}_url_insert");
                DB::unprepared("DROP TRIGGER IF EXISTS before_tb_tree_{$lang}_url_update");
            }
            DB::statement('DROP TRIGGER IF EXISTS before_tb_tree_insert');
            DB::statement('DROP TRIGGER IF EXISTS before_tb_tree_update');
        }

        Schema::dropIfExists('revisions');
        Schema::dropIfExists('tb_tree');
        Schema::dropIfExists('settings');
    }
};
