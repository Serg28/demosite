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
        Schema::create('hotline_categories', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->json('name')->nullable();
            $table->json('title')->nullable();
            //$table->unsignedBigInteger('google_id')->unique();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE hotline_categories ADD INDEX title_idx (title(700))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotline_categories');
    }
};
