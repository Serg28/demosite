<?php

namespace App\Console\Commands;

use App\Services\CheckboxUa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LegalEntitiesRecipient;

class CloseShiftCheckbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkbox:shiftclose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Закрытие смены Checkbox.ua для текущего ФОПа';

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
        Log::info('---Starting CRON CloseShiftCheckbox---');

        $recipients = LegalEntitiesRecipient::all();

        foreach($recipients as $recipient) {
            $result = (new CheckboxUa($recipient))->closeShift();
            Log::info('CRON CloseShiftCheckbox, closing recipient ID '.$recipient->id . ': '. print_r($result, 1));
            sleep(2);
        }

        Log::info('---Finish CRON CloseShiftCheckbox---');
    }

}
