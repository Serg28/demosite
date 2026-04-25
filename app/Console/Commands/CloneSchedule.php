<?php

namespace App\Console\Commands;

use App\Services\JustinApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloneSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clone:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
// Получаем записи из delivery_schedules с warehouse_id = 12
        $deliverySchedules = DB::table('delivery_schedules')->where('warehouse_id', 12)->get();

// Проходимся по полученным записям
        foreach ($deliverySchedules as $deliverySchedule) {
            // Копируем запись с новым warehouse_id = 1
            $newDeliveryScheduleId = DB::table('delivery_schedules')->insertGetId([
                'warehouse_id' => 18,
                'day_of_week' => $deliverySchedule->day_of_week,
                'start_time' => $deliverySchedule->start_time,
                'end_time' => $deliverySchedule->end_time,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Получаем связанные записи из warehouse_delivery_schedule_info
            $deliveryScheduleInfo = DB::table('warehouse_delivery_schedule_info')
                ->where('delivery_schedules_id', $deliverySchedule->id)
                ->get();

            // Проходимся по полученным записям и копируем их с новым delivery_schedules_id
            foreach ($deliveryScheduleInfo as $info) {
                DB::table('warehouse_delivery_schedule_info')->insert([
                    'delivery_id' => $info->delivery_id,
                    'delivery_schedules_id' => $newDeliveryScheduleId,
                    'days_to_delivery' => $info->days_to_delivery,
                    'description' => $info->description,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'priority' => $info->priority,
                ]);
            }
        }
    }
}
