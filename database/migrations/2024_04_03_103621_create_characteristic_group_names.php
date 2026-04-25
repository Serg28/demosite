<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('characteristic_group_names', function (Blueprint $table) {
            $table->id();

            $table->json('title')->nullable()->comment('Название группы');
            $table->string('id_1c', 150)->index()->nullable();
            $table->integer('priority')->default(0)->index();

            //$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characteristic_group_names');
    }
};
