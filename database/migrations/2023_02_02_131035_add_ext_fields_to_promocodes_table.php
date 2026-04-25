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
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->tinyInteger('use_for_installments')->index();
            $table->tinyInteger('use_for_promotional')->index();
            $table->tinyInteger('use_for_discount_cards')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['use_for_installments', 'use_for_promotional', 'use_for_discount_cards']);
        });
    }
};
