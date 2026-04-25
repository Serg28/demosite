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
        //Закомментировано в связи с импортом в дампе
        /*DB::table('orders_status')->where('id', 1)->update([
            'name_for_prom' => 'pending',
        ]);

        DB::table('orders_status')->where('id', 2)->update([
            'name_for_prom' => 'received',
        ]);

        DB::table('orders_status')->where('id', 7)->update([
            'name_for_prom' => 'delivered',
        ]);

        DB::table('orders_status')->where('id', 10)->update([
            'name_for_prom' => 'canceled',
        ]);

        DB::table('orders_status')->insert([
            'title' => json_encode([
                'ua' => 'Оплачений',
                'ru' => 'Оплаченый',
            ]),
            'name_for_prom' => 'paid',
        ]);*/
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
