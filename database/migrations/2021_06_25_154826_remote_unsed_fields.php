<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoteUnsedFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at', 'title_ru', 'title_en', 'settings', 'picture', 'picture_ru', 'picture_en']);

            $table->json('title')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->timestamps();
            $table->string('title_ru');
            $table->string('title_en');
            $table->text('settings');
            $table->string('picture');
            $table->string('picture_ru');
            $table->string('picture_en');
        });
    }
}
