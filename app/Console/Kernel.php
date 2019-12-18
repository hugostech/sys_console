<?php

namespace App\Console;

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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('email:luckydraw')
//                 ->dailyAt('20:00');
    }
}
