<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        \App\Console\Commands\SendRecentPostsEmail::class,
        \App\Console\Commands\ExpireSmartAds::class,
        // \App\Console\Commands\SendCustomPostNotifications::class,
        \App\Console\Commands\PlanExpiry::class,
        \App\Console\Commands\FetchRssFeeds::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('rss:fetch')->everyFifteenMinutes();
        $schedule->command('email:send-recent-posts')->everyFourHours();
        // $schedule->command('posts:send-custom-notifications')->everySecond();63
        $schedule->command('expired:plan')->daily();
        $schedule->command('ads:expire')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
