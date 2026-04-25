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
        Schema::table('orders', function (Blueprint $table) {
            //$table->unsignedInteger('pay_status_id')->index()->nullable();
            $table->unsignedBigInteger('is_online_payed')->default(0)->change();
            $table->unsignedBigInteger('complect_status_id')->index()->nullable();

            $table->foreign('is_online_payed')
                ->on('pay_status')
                ->references('id')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('complect_status_id')
                ->on('complect_status')
                ->references('id')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['complect_status_id']);
            $table->boolean('is_online_payed')->default(false)->change();
        });
    }
};
