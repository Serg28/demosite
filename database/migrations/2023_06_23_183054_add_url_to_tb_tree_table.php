<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_tree', function (Blueprint $table) {
            $table->json('url')->after('slug')->nullable();
        });
        DB::statement('ALTER TABLE tb_tree ADD UNIQUE INDEX tb_tree_url_unique (url(100))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_tree', function (Blueprint $table) {
            $table->dropColumn('url');
        });
        DB::statement('ALTER TABLE tb_tree DROP INDEX tb_tree_url_unique');
    }
};
