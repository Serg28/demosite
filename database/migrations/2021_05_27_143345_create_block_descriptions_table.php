<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('block_descriptions')) {
            Schema::create('block_descriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('block_id')->index();
                $table->json('description');

                $table->foreign('block_id')->on('blocks')->references('id')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_descriptions');
    }
}
