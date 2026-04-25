<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIconToPictureOnBlockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('block_why_we', function (Blueprint $table) {
            $table->renameColumn('icon', 'picture');
        });

        Schema::table('block_advantages', function (Blueprint $table) {
            $table->renameColumn('icon', 'picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('block_why_we', function (Blueprint $table) {
            $table->renameColumn('picture', 'icon');
        });

        Schema::table('block_advantages', function (Blueprint $table) {
            $table->renameColumn('picture', 'icon');
        });
    }
}
