<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLegalEntitiesRecipientsTable extends Migration
{
    public function up(): void
    {
        Schema::create('legal_entities_recipients', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->id();
            $table->json('title');
            $table->integer('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_entities_recipients');
    }
}
