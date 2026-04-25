<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCharacteristicOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_characteristic_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedInteger('characteristic_id')->index();
            $table->unsignedInteger('characteristic_option_id')->index();
            $table->integer('priority')->default(0);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('characteristic_id')->references('id')->on('characteristics')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('characteristic_option_id')->references('id')->on('characteristic_options')->onDelete('restrict')->onUpdate('cascade');
        });

        DB::statement('ALTER TABLE `product_characteristic_options` ADD UNIQUE `product_id_characteristic_id_characteristic_option_id` (`product_id`, `characteristic_id`, `characteristic_option_id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_characteristic_options');

        DB::statement('ALTER TABLE product_characteristic_options DROP INDEX product_id_characteristic_id_characteristic_option_id');
    }
}
