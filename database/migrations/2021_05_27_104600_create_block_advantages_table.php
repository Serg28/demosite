<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockAdvantagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_advantages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_id')->nullable();
            $table->json('title')->nullable();
            $table->string('icon')->nullable();
            $table->integer('priority')->default(0);
            $table->foreign('block_id')->on('blocks')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_advantages');
    }
}
