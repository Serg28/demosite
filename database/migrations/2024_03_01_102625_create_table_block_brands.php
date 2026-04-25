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
        Schema::create('block_popular_brands', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id')->nullable()->index();
            $table->unsignedInteger('brand_id')->nullable()->index();
            $table->integer('priority')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_target_blank')->default(0);

            $table->foreign('block_id')->references('id')
                ->on('blocks')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('brand_id')->references('id')
                ->on('brands')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_popular_brands');
    }
};
