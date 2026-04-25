<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactManiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_manies', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->tinyInteger('is_active');
            $table->string('picture');
            $table->text('text')->nullable();
            $table->integer('priority')->nullable();

            $table->unsignedBigInteger('contact_rubric_id');

            $table->foreign('contact_rubric_id')
                ->on('block_contacts_rubrics')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_manies');
    }
}
