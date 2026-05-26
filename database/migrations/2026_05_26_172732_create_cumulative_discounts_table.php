<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cumulative_discounts', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_sum', 12, 2)->comment('Поріг накопиченої суми замовлень (грн)');
            $table->unsignedTinyInteger('discount')->comment('Відсоток знижки (%)');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('discount_cumulative')->nullable()->after('phone')
                  ->comment('Накопичена знижка (%) — оновлюється OrderObserver');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('discount_cumulative');
        });

        Schema::dropIfExists('cumulative_discounts');
    }
};
