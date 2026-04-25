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
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->json('name')->nullable();
            $table->tinyInteger('is_closed')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_characteristic', function (Blueprint $table) {
            $table->dropColumn(['name', 'is_closed']);
        });
    }
};
