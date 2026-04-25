<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('street')->nullable()->comment('Улица')->after('address');
            $table->string('house')->nullable()->comment('Дом')->after('address');
            $table->string('apartment')->nullable()->comment('Квартира')->after('address');
            $table->string('building')->nullable()->comment('Корпус')->after('address');
            $table->integer('floor')->nullable()->comment('Этаж')->after('address');
            $table->boolean('is_elevator')->default(false)->comment('Имеется лифт')->after('address');
            $table->boolean('is_lifting')->default(false)->comment('Услуга поднятия на этаж')->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('house');
            $table->dropColumn('apartment');
            $table->dropColumn('building');
            $table->dropColumn('floor');
            $table->dropColumn('is_elevator');
            $table->dropColumn('is_lifting');
        });
    }
};
