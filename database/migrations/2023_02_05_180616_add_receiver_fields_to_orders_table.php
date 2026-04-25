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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('patronymic')->after('last_name')->nullable();
            $table->string('receiver')->after('patronymic')->default('user');
            $table->string('receiver_first_name')->after('receiver')->nullable();
            $table->string('receiver_last_name')->after('receiver_first_name')->nullable();
            $table->string('receiver_patronymic_name')->after('receiver_last_name')->nullable();
            $table->string('receiver_phone')->after('receiver_patronymic_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['patronymic']);
            $table->dropColumn(['receiver']);
            $table->dropColumn(['receiver_first_name']);
            $table->dropColumn(['receiver_last_name']);
            $table->dropColumn(['receiver_patronymic_name']);
            $table->dropColumn(['receiver_phone']);
        });
    }
};
