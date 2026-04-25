<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLegalEntitiesRecipientIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('legal_entities_recipient_id')->index()->nullable();
            $table->foreign('legal_entities_recipient_id')->references('id')->on('legal_entities_recipients');
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
            $table->dropForeign(['legal_entities_recipient_id']);
            $table->dropColumn('legal_entities_recipient_id');
        });
    }
}
