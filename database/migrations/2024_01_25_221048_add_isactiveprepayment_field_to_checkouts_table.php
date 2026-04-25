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
        Schema::table('checkouts', function (Blueprint $table) {
            $table->tinyInteger('is_active_prepayment')->default(0)->comment('Активировать предоплату. 1 - Да');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn(['is_active_prepayment']);
        });
    }
};
