<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteColumnsOrderReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_receipts', function (Blueprint $table) {
            $table->dropColumn(['link', 'is_ready']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_receipts', function (Blueprint $table) {
            $table->string('link');
            $table->boolean('is_ready')->default(false);
        });
    }
}
