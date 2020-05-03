<?php

namespace App\Console;

use App\Console\Commands\CategoryAlign;
use App\Console\Commands\CleanSpecialWithoutStock;
use App\Console\Commands\DailyStockSync;
use App\Console\Commands\GenerateGoogleFeed;
use App\Console\Commands\GenerateProductFeed;
use App\Console\Commands\ImportCSV;
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
        Commands\Inspire::class,
        Commands\SendLuckyDrawEmail::class,
        ImportCSV::class,
        GenerateProductFeed::class,
        CleanSpecialWithoutStock::class,
        DailyStockSync::class,
        CategoryAlign::class,
        GenerateGoogleFeed::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //generate product feed for marketing
        $schedule->command('csv:generate')->dailyAt('07:00');
        $schedule->command('csv:generate')->dailyAt('19:00');
        $schedule->command('csv:generate --pcpicker')->dailyAt('07:20');

        //daily sync stock
        $schedule->command('stock:sync')->dailyAt('18:00');

        //delete product special if product out of stock
        $schedule->command('special:clear-up')->dailyAt('18:30');

        //csv import
        $schedule->command('csv:read')->weekdays()->at('23:30');

        //align products
        $schedule->command('category:align')->dailyAt('03:30');

    }
}
