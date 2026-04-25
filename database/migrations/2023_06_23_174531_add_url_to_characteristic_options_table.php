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
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->json('url')->after('slug')->nullable();
        });
        DB::statement('ALTER TABLE characteristic_options ADD UNIQUE INDEX characteristic_options_url_unique (url(100))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->dropColumn('url');
        });
        DB::statement('ALTER TABLE characteristic_options DROP INDEX characteristic_options_url_unique');
    }
};
