<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSettingsForV3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('setting_select');

        Schema::table('settings', function (Blueprint $table) {
            $table->string('type')->change();
            $table->string('value')->change();
            $table->renameColumn('group_type', 'group');
            $table->json('value_languages')->nullable();
            $table->string('file');
            $table->tinyInteger('check');
            $table->json('textarea_with_languages')->nullable();
            $table->json('froala_with_languages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_select', function (Blueprint $table) {
            $table->unsignedInteger('id_setting');
            $table->string('value');
            $table->string('value2');
            $table->string('value3');
            $table->integer('priority');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->integer('type')->change();
            $table->json('value')->change();
            $table->renameColumn('group', 'group_type');
            $table->dropColumn(['value_languages', 'file', 'check', 'textarea_with_languages', 'froala_with_languages']);
        });
    }
}
