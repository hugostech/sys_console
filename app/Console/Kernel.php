<?php

namespace App\Console;

use App\Console\Commands\CleanSpecialWithoutStock;
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

        //delete product special if product out of stock
        $schedule->command('special:clear-up')->dailyAt('18:30');


        //csv import
        $schedule->command('csv:read')->dailyAt('01:30');

    }
}
