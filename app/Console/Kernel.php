<?php

namespace App\Console;

use App\Console\Commands\CloseShiftCheckbox;
use App\Console\Commands\HelperMakeCommand;
use App\Console\Commands\ImportFromProm;
use App\Console\Commands\Justin;
use App\Console\Commands\Meest;
use App\Console\Commands\NovaPoshta;
use App\Console\Commands\ReminderReserveTomorrowOrder;
use App\Console\Commands\ReminderReserveTodayOrder;
use App\Console\Commands\ReminderUnfinishedBasket;
use App\Console\Commands\ReminderUnpaidReserveTodayOrder;
use App\Console\Commands\ReminderUnpaidReserveTomorrowOrders;
use App\Console\Commands\SalesDriveProducts;
use App\Console\Commands\Ukrposhta;
use App\Console\Commands\ViewCompileCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        NovaPoshta::class,
        Ukrposhta::class,
        //Justin::class,
        //CloseShiftCheckbox::class,
        //SalesDriveProducts::class,
        //NovaPoshta::class,
        //Meest::class,
        //ImportFromProm::class,
        HelperMakeCommand::class,
        ReminderReserveTomorrowOrder::class,
        ReminderReserveTodayOrder::class,
        ReminderUnpaidReserveTodayOrder::class,
        ReminderUnpaidReserveTomorrowOrders::class,
        ReminderUnfinishedBasket::class,
        ViewCompileCommand::class
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('api:meest')->dailyAt('02:00');
        //$schedule->command('api:ukrposhta')->dailyAt('03:00');
        //$schedule->command('api:np_cities_and_warehouse')->dailyAt('04:00');
        //$schedule->command('api:justin')->dailyAt('05:00');
        #$schedule->command('checkbox:shiftclose')->dailyAt('23:59');

        //$schedule->command('orders:reminder_unfinished_basket')->dailyAt('12:00');

        //$schedule->command('order:remainder_reserve_today_order')->dailyAt('01:01');
        //$schedule->command('order:remainder_unpaid_reserve_today_order')->dailyAt('01:10');
        //$schedule->command('order:remainder_reserve_tomorrow_order')->dailyAt('07:00');
        //$schedule->command('order:remainder_unpaid_reserve_tomorrow_order')->dailyAt('07:10');
        //$schedule->command('order:check_np_statuses')->hourly();

        //$schedule->command('instagram-feed:refresh '.setting('count_instagram_feeds').'')->twiceDaily();
        //$schedule->command('instagram-feed:refresh-tokens')->monthlyOn(1, '01:00');

        //$schedule->command('clear_revision')->dailyAt("06:00"); //все ревизии
        $schedule->command('clear_revision_without_orders')->dailyAt('03:00'); //ревизии без заказов
        $schedule->command('clear_revision_orders')->dailyAt('03:30'); //ревизии заказов

        $schedule->command('feed:feeds_to_files')->dailyAt('01:30');
        $schedule->command('sitemap:google_to_files')->dailyAt('01:50');

        $schedule->command('categories:maintenance')->cron('0 1-23/2 * * *'); //каждый нечетный час
        $schedule->command('catalog:maintenance')->cron('0 0-22/2 * * *'); //каждый четный час

        //$schedule->command('orders:import_from_prom')->everyFiveMinutes();

        //$schedule->command('search:check_product_index')->everyTenMinutes();

        //Если не будет работать супервизор
        /*
        $schedule->command('queue:work --queue=high,shop-emails,shop-sms,broadcasting,shop-order,shop-rabbit,default,low --timeout=3600 --sleep=3 --tries=3 --max-time=3600')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('queue:work redis_shop_reindex --queue=shop-reindexProducts --timeout=3600 --sleep=3 --tries=3 --max-time=3600')
            ->everyMinute()
            ->withoutOverlapping();*/
    }

    /**
     * Register the commands for the application
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
