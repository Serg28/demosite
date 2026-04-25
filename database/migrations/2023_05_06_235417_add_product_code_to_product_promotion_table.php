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
        Schema::table('product_promotion', function (Blueprint $table) {
            $table->string('product_code')->after('id')->nullable();
            /*$table->foreign('product_code')->references('code')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_promotion', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });
    }
};
