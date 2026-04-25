<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInNewToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_new')->index();
            $table->integer('quantity')->default(0);
            $table->tinyInteger('guarantee')->default(1);
            $table->string('guarantee_type');
            $table->string('guarantee_period')->default(12);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_new']);
            $table->dropColumn(['is_new', 'quantity', 'guarantee', 'guarantee_type', 'guarantee_period']);
        });
    }
}
