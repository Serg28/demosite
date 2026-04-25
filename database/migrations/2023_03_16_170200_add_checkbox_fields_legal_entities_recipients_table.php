<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('legal_entities_recipients', function (Blueprint $table) {
            $table->text('checkbox_domain')->nullable();
            $table->text('checkbox_login')->nullable();
            $table->text('checkbox_password')->nullable();
            $table->text('checkbox_license_key')->nullable();
            $table->text('checkbox_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('legal_entities_recipients', function (Blueprint $table) {
            $table->dropColumn(['checkbox_domain', 'checkbox_login', 'checkbox_password', 'checkbox_license_key', 'checkbox_token']);
        });
    }
};
