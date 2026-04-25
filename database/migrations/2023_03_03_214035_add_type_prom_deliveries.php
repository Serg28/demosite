<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('deliveries')->where('id', 1)->update([
            'prom_delivery_id' => 15229226,
        ]);
        DB::table('deliveries')->where('id', 3)->update([
            'prom_delivery_id' => 15229222,
        ]);

        DB::table('deliveries')->where('id', 2)->update([
            'prom_delivery_id' => 15229225,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
