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
        Schema::create('google_categories', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->json('title')->nullable();
            //$table->unsignedBigInteger('google_id')->unique();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE google_categories ADD INDEX title_idx (title(700))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_categories');
    }
};
