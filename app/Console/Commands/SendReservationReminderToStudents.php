<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\Helpers\DRREmail;
use App\Helpers\MailHelper;
use App\Helpers\NotificationHelper;
use App\Mail\ReminderEmail;
use App\Schedules;
use App\SchoolOffDays;
use App\Settings;
use App\Students;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationReminderToStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:send_reservation_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder to students for today\'s registered classes';

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
        $timezone = CommonHelper::getSchoolTimezone();
        $date = Carbon::now($timezone)->format('Y-m-d');

        // Do not send email if school off day
        $is_school_off_day = SchoolOffDays::where('date',$date)->exists();
        if($is_school_off_day)
        {
            return;
        }

        // Check if this is the right time to send reminder email.
        $now = Carbon::now($timezone)->format('H:i');
        $student_reminder_email_time = Settings::get_value('student_reminder_email_time');
        if($now != $student_reminder_email_time)
        {
            // Log::info("No time to run this script current Hour " . $now. " Scheduled to run at " . $student_reminder_email_time." (TZ - ".$timezone.")");
            return;
        }

        // Logic to check which types of lessons to send reminder for
        $schedule_types_to_send_reminder_for = [];
        $active_lesson_types = explode(',',Settings::get_value('student_reminder_email_lesson_types'));
        if(in_array("event",$active_lesson_types))
        {
            foreach(Schedules::EVENT_TYPES as $type){
                $schedule_types_to_send_reminder_for[] = $type;
            }

        }
        if(in_array("class" ,$active_lesson_types))
        {
            foreach(Schedules::CLASS_TYPES as $type){
                $schedule_types_to_send_reminder_for[] = $type;
            }
        }
        if(count($schedule_types_to_send_reminder_for) == 0)
        {
            return;
        }

        $students = Students::with('user')->whereHas('user', function($query){
                        $query->where('receive_emails',1)
                            ->orWhere('receive_line_messsges', 1);
                    })->get();

        // Fetch reservations of each student and send email
        foreach($students as $student)
        {
            NotificationHelper::sendDailyReservationReminder($student, $date, $schedule_types_to_send_reminder_for);
        }
    }
}
