<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\ScheduleZoomMeeting;
use App\Settings;
use App\Yoyaku;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendZoomMeetingReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:zoom_meeting_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder email before zoom meetings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current_time = Carbon::now();
        $use_zoom = Settings::get_value('use_zoom');
        $zoom_email_notification_to = explode(',',Settings::get_value('zoom_email_notification_to'));
        $zoom_email_notification_before = Settings::get_value('zoom_email_notification_before');

        if($use_zoom && $zoom_email_notification_to)
        {
            if((int)$zoom_email_notification_before > 0)
            {
                $meeting_start_time = (clone $current_time)->addMinutes($zoom_email_notification_before)->format('Y-m-d H:i:00');
                $scheduleZoomMeetings = ScheduleZoomMeeting::with('zoomMeeting')->whereHas('zoomMeeting', function($query) use ($meeting_start_time) {
                                            $query->where('start_time', $meeting_start_time);
                                        })->get();

                foreach($scheduleZoomMeetings as $scheduleZoomMeeting)
                {
                    $schedule = $scheduleZoomMeeting->schedule;
                    $date = $scheduleZoomMeeting->date;
                    $zoomMeeting = $scheduleZoomMeeting->zoomMeeting;

                    if(in_array('student', $zoom_email_notification_to))
                    {                     
                        $yoyakus = Yoyaku::where('schedule_id', $schedule->id)
                                        ->where('date', $date)
                                        ->where('waitlist',0)
                                        ->where('status',0)->get();                                      
                        foreach($yoyakus as $yoyaku)
                        {
                            $user = $yoyaku->student->user;
                            NotificationHelper::sendZoomMeetingReminderForClass($user, $schedule, $date, $zoomMeeting);
                        }
                    }

                    if(in_array('teacher', $zoom_email_notification_to))
                    {
                        $user = $schedule->teacher->user;
                        NotificationHelper::sendZoomMeetingReminderForClass($user, $schedule, $date, $zoomMeeting, true);
                    }
                }
            }
        }
    }
}
