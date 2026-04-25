<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_characteristic_options', function (Blueprint $table) {
            $table->json('characteristic_option_value')->nullable()->comment('Json текстовое значение, если не указан characteristic_option_id ');

            $table->unsignedInteger('characteristic_option_id')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_characteristic_options', function (Blueprint $table) {
            $table->dropColumn('characteristic_option_value');

            $table->unsignedInteger('characteristic_option_id')->nullable(false)->default(null)->change();
        });
    }
};
