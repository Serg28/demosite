<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->tinyInteger('is_payparts')->default(0)->comment('Подразумевает оплату частями или кредит. 1 - Да');
        });
    }

    public function down(): void
    {
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn('is_payparts');
        });
    }
};
