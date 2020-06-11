<?php

namespace App\Helpers;

use App\EmailTemplates;
use App\Schedules;
use App\Settings;
use App\Yoyaku;

class DRREmail {

    private $email_template;
    private $schedule_types_to_send_reminder_for;
    private $student;
    private $date;
    private $lang;
    private $class_reminder;
    private $event_reminder;
    
    public function __construct()
    {
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

        $this->email_template = EmailTemplates::get_by_name('daily_reservation_reminder');
        $this->schedule_types_to_send_reminder_for = $schedule_types_to_send_reminder_for;
    }

    public function get_email_template()
    {
        return  $this->email_template;
    }

    public function get_schedule_types_to_send_reminder_for()
    {
        return $this->schedule_types_to_send_reminder_for;
    }

    public function set_student($student)
    {
        $this->student = $student;
    }

    public function set_date($date)
    {
        $this->date = $date;
    }

    public function set_lang($lang)
    {
        $this->lang = $lang;
    }

    public function set_class_reminder($class_reminder)
    {
        $this->class_reminder = $class_reminder;
    }
    public function set_event_reminder($event_reminder)
    {
        $this->event_reminder = $event_reminder;
    }


    public function get_email_data()
    {
        $out['subject'] = NULL;
        $out['content'] = NULL;
        $out['header'] = NULL;
        $out['footer'] = NULL;

        $old_lang = app()->getLocale();
        $yoyakus = Yoyaku::with('schedule')->where('customer_id', $this->student->id)
                ->where('date', $this->date)
                ->where('waitlist', 0)
                ->where('status','!=',2)
                ->whereHas('schedule', function ($query){
                    $query->whereIn('type', $this->schedule_types_to_send_reminder_for);
                })
                ->get();

        if($yoyakus->count() > 0)
        {
            app()->setLocale($this->lang);

            // Set email subject, content
            $reserverdEvents = $reservedClasses = [];
            foreach($yoyakus as $yoyaku)
            {
                if($yoyaku->schedule->is_event())
                {
                    $reserverdEvents[] = $yoyaku;
                }
                else if($yoyaku->schedule->is_class())
                {
                    $zoomMeeting = NULL;
                    $scheduleZoomMeeting = $yoyaku->schedule->getScheduleZoomMeeting($yoyaku->date);
                    if($scheduleZoomMeeting)
                    {
                        $zoomMeeting = $scheduleZoomMeeting->zoomMeeting;
                    }

                    $reservedClasses[] = [
                        'yoyaku' => $yoyaku,
                        'zoomMeeting' => $zoomMeeting
                    ];
                }
            }
            $reminder = ['class_reminder' => $this->class_reminder, 'event_reminder' => $this->event_reminder];
            $button_texts = $this->email_template->buttonTextsByLang($this->lang);
            $reserved_classes_section = view('emails.daily_reservation_reminder.reserved_classes_section',compact('reservedClasses','button_texts','reminder'))->render();
            $reserved_events_section = view('emails.daily_reservation_reminder.reserved_events_section',compact('reserverdEvents','button_texts','reminder'))->render();
            $signin_btn = view('emails.daily_reservation_reminder.signin_btn', compact('button_texts'))->render();

            $this->email_template->set_student_name($this->student->getfullNameForEmail($this->lang))
                    ->set_date($this->date)
                    ->set_reserved_classes_section($reserved_classes_section)
                    ->set_reserved_events_section($reserved_events_section)
                    ->set_signin_btn($signin_btn)
                    ->set_username($this->student->user->username);
            $subject = $this->email_template->get_format('subject_'.$this->lang);
            $content = $this->email_template->get_format('content_'.$this->lang);
            $header = $this->email_template->get_header($this->lang);
            $footer = $this->email_template->get_footer($this->lang);

            $out['subject'] = $subject;
            $out['content'] = $content;
            $out['header'] = $header;
            $out['footer'] = $footer;
        }

        app()->setLocale($old_lang);
        return $out;
    }
}

?>
