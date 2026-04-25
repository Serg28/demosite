<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->index()->default(null);

            $table->foreign('group_id')->references('id')->on('characteristic_group_names')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->dropColumn(['group_id']);
        });
    }
};
