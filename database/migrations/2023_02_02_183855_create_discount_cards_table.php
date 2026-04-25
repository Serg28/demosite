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
        Schema::create('discount_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('barcode');
            $table->string('discount');
            $table->string('name');
            $table->string('phone')->index();
            $table->string('email')->index();
            $table->string('nikname')->index();
            $table->date('regdate');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->string('address');
            $table->text('comment');
            $table->text('ip');
            $table->tinyInteger('is_active')->index();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users');
            //->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('discount_cards');
    }
};
