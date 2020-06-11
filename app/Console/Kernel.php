<?php

namespace App\Console;

use App\Settings;
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
        \App\Console\Commands\SendReservationReminderToStudents::class,
        \App\Console\Commands\ExpirePasswordResetTokens::class,
        \App\Console\Commands\RefreshAutomatedTags::class,
        \App\Console\Commands\SendZoomMeetingReminderEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('student:send_reservation_reminder')->everyMinute();
        $schedule->command('email:zoom_meeting_notification')->everyMinute();
        $schedule->command('expire:password_reset_tokens')->hourly();
        
        $timezone = NULL;
        try {
            $timezone = Settings::get_value('school_timezone');
        } catch (\Exception $e) {
            \Log::warning('School timezone reading failed, on or more cron might not run as expected.');    
        }

        if($timezone)
        {
            // runs at 12:00 every night
            $schedule->command('student:refresh_automated_tags')->daily()->timezone($timezone);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
