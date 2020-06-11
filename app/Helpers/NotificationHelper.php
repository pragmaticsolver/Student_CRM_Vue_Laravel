<?php


namespace App\Helpers;

use App\Classes;
use App\EmailTemplates;
use App\Schedules;
use App\Settings;
use App\Students;
use App\Yoyaku;

class NotificationHelper {

    var $user;
    var $lang;
    var $emailTemplate;
    var $lineMessageBulderData;

    var $emailsToBeAlwaysSent = [
        'password_reset_link_notification',
        'verify_email_notification',
        'new_user_notification'
    ];

    public function __construct($user)
    {
        $this->user = $user;
        $this->lang = $user->get_lang();
        $this->lineMessageBulderData = [];
    }

    public function loadTemplate($name)
    {
        $this->emailTemplate = EmailTemplates::get_by_name($name);
        
        // Set Global Variables
        $this->emailTemplate->set_username($this->user->username);
        
        return $this->emailTemplate;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setTemplate($emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    public function setlineMessageBulderData($data)
    {
        $this->lineMessageBulderData = $data;
    }

    public function shouldSendEmail()
    {
        if(in_array($this->emailTemplate->name, $this->emailsToBeAlwaysSent)) {
            return true;
        }
        return $this->user->receive_emails == 1;
    }

    public function send()
    {
        // Send Email
        if($this->shouldSendEmail())
        {
            MailHelper::sendMail($this->user->getEmailAddress(),
                $this->emailTemplate->get_format('content_'.$this->lang),
                $this->emailTemplate->get_format('subject_'.$this->lang),
                $this->emailTemplate->get_header($this->lang),
                $this->emailTemplate->get_footer($this->lang)
            ); 
        }

        // Send Line Message
        if(Settings::get_value('use_line_messaging_api') && $this->user->receive_line_messsges == 1 && $this->user->line_user_id)
        {
            $messgeBuilder = $this->emailTemplate->getLineMessageBuilder($this->lang, $this->lineMessageBulderData);
            if($messgeBuilder) {
                LineHelper::sendMessgeInBackground($this->user->line_user_id, $messgeBuilder);
            }
        }
    }

    public static function sendRegisterClassNotification($student, $schedule, $dates_registered)
    {
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('register_class_notification_repeat');
        if($emailTemplate->enable == 1)
        {
            $class = $schedule->class;
            $emailTemplate
                ->set_class_name($class->title)
                ->set_student_name($student->getfullNameForEmail($notificationHelper->getLang()))
                ->set_date(implode(', ',$dates_registered))
                ->set_time($schedule->start_time.' - '.$schedule->end_time);
            
            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendRegisterClassNotificationToTeacher($student, $schedule, $dates_registered)
    {
        $teacher = $schedule->teacher;
        $user = $teacher->user;
        
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('register_class_notification_to_teacher');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $class = $schedule->class;

            $emailTemplate->set_class_name($class->title)
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_date(implode(', ',$dates_registered))
                ->set_time($schedule->start_time.' - '.$schedule->end_time)
                ->set_teacher_name($teacher->name);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendWaitlistClassNotification($yoyaku)
    {
        $student = $yoyaku->student;
        $user = $student->user;
        
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('waitlist_class_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $schedule = $yoyaku->schedule;
            $class = $schedule->class;

            $emailTemplate
                ->set_class_name($class->title)
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_date($yoyaku->date)
                ->set_time($schedule->start_time.' - '.$schedule->end_time);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendCancelClassNotification($student_id, $schedule_id, $dates_cancelled)
    {
        $student = Students::find($student_id);
        $user = $student->user;
        
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('cancel_class_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $schedule = Schedules::find($schedule_id);
            $class = $schedule->class;

            $emailTemplate
                    ->set_class_name($class->title)
                    ->set_student_name($student->getfullNameForEmail($lang))
                    ->set_date(implode(', ',$dates_cancelled))
                    ->set_time($schedule->start_time.' - '.$schedule->end_time);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendCheckinNotification($student, $date_time)
    {
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('checkin_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();

            $emailTemplate
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_date_time($date_time);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendCheckoutNotification($student, $date_time)
    {
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('checkout_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();

            $emailTemplate
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_date_time($date_time);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendCancelReservationNotifyWatlist($yoyaku)
    {
        $user = $yoyaku->student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('cancel_reservation_notify_waitlist');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $encrypted_id = encrypt($yoyaku->id);

            $reserve_button_url = route('waitlist.reserve',$encrypted_id);
            $cancel_waitlist_btn_url = route('waitlist.cancel',$encrypted_id);
            
            $button_texts = $emailTemplate->buttonTextsByLang($lang);
            $action_btns = view('emails.cancel_reservation_notify_waitlist.action_btns', compact('button_texts','reserve_button_url','cancel_waitlist_btn_url'))->render();
            
            $emailTemplate->set_class_name($yoyaku->schedule->class->title)
                ->set_student_name($yoyaku->student->getfullNameForEmail($lang))
                ->set_date($yoyaku->date)
                ->set_action_btns($action_btns)
                ->set_reserve_btn_url($reserve_button_url)
                ->set_cancel_waitlist_btn_url($cancel_waitlist_btn_url);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendTestNotification($student_test, $schedule_lesson)
    {
        $student = $student_test->student;
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('test_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $lesson = $schedule_lesson->lesson;
            $course = $lesson->course;
            $unit = $lesson->unit;

            $emailTemplate
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_course($course->title)
                ->set_unit($unit->name)
                ->set_lesson($lesson->title)
                ->set_date($schedule_lesson->date)
                ->set_test($student_test->test->name)
                ->set_test_url(route('student.online_test.take', $student_test->id));

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendPaperTestNotification($paper_test, $schedule_lesson)
    {
        $schedule = $schedule_lesson->schedule;
        $teacher = $schedule->teacher;
        $user = $teacher->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('paper_test_notification');
        if($emailTemplate->enable == 1)
        {
            $lesson = $schedule_lesson->lesson;
            $course = $lesson->course;
            $unit = $lesson->unit;

            $emailTemplate
                ->set_teacher_name($teacher->name)
                ->set_course($course->title)
                ->set_unit($unit->name)
                ->set_lesson($lesson->title)
                ->set_date($schedule_lesson->date)
                ->set_test($paper_test->name)
                ->set_test_url(route('student.paper_test.create', $schedule->id));

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendAutomaticAssessmentNotification($assessment_user,$schedule_lesson)
    {
        $user = $assessment_user->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('automatic_assessment_notification');
        if($emailTemplate->enable == 1)
        {
            $lesson = $schedule_lesson->lesson;
            $course = $lesson->course;
            $unit = $lesson->unit;

            $emailTemplate
                ->set_user_name($user->name)
                ->set_course($course->title)
                ->set_unit($unit->name)
                ->set_lesson($lesson->title)
                ->set_date($schedule_lesson->date)
                ->set_assessment($assessment_user->assessment->name)
                ->set_assessment_url(route('user.assessment.take', $assessment_user->id));

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendManualAssessmentNotification($assessment_user)
    {
        $user = $assessment_user->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('manual_assessment_notification');
        if($emailTemplate->enable == 1)
        {
            $emailTemplate
                ->set_user_name($user->name)
                ->set_assessment($assessment_user->assessment->name)
                ->set_assessment_url(route('user.assessment.take', $assessment_user->id));

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendAssessmentResultAvailableNotification($student, $class, $view_assessment_url)
    {
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('assessment_result_available_notification');
        if($emailTemplate->enable == 1)
        {
            $lang = $notificationHelper->getLang();
            $emailTemplate
                ->set_student_name($student->getfullNameForEmail($lang))
                ->set_class_name($class->title)
                ->set_view_assessment_url($view_assessment_url);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendResetPasswordLinkNotification($user, $reset_password_link)
    {
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('password_reset_link_notification');
        if($emailTemplate->enable == 1)
        {
            $emailTemplate
                ->set_user_name($user->name)
                ->set_reset_password_link($reset_password_link);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendZoomMeetingReminderForClass($user, $schedule, $date, $zoomMeeting, $use_start_url = false)
    {
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('zoom_meeting_reminder_for_class');
        if($emailTemplate->enable == 1)
        {
            $class = Classes::find($schedule->class_id);

            $emailTemplate
                ->set_class_name($class->title)
                ->set_date($date)
                ->set_time($schedule->start_time.' - '.$schedule->end_time)
                ->set_zoom_meeting_url($use_start_url ? $zoomMeeting->start_url : $zoomMeeting->join_url)
                ->set_zoom_meeting_id($zoomMeeting->display_meeting_id)
                ->set_zoom_meeting_password($zoomMeeting->password);

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendDailyReservationReminder($student, $date, $schedule_types_to_send_reminder_for)
    {
        $user = $student->user;
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('daily_reservation_reminder');
        if($emailTemplate->enable == 1)
        {
            $old_lang = app()->getLocale();
            $lang = $notificationHelper->getLang();
            
            $yoyakus = Yoyaku::with('schedule')->where('customer_id', $student->id)
                    ->where('date', $date)
                    ->where('waitlist', 0)
                    ->where('status','!=',2)
                    ->whereHas('schedule', function ($query) use ($schedule_types_to_send_reminder_for){
                        $query->whereIn('type', $schedule_types_to_send_reminder_for);
                    })
                    ->get();

            if($yoyakus->count() > 0)
            {
                app()->setLocale($lang);

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
                
                $reminder = ['class_reminder' => Settings::get_value('class_reminder'), 'event_reminder' => Settings::get_value('event_reminder')];
            
                $button_texts = $emailTemplate->buttonTextsByLang($lang);
                $reserved_classes_section = view('emails.daily_reservation_reminder.reserved_classes_section',compact('reservedClasses','button_texts','reminder'))->render();
                $reserved_events_section = view('emails.daily_reservation_reminder.reserved_events_section',compact('reserverdEvents','button_texts','reminder'))->render();
                $signin_btn = view('emails.daily_reservation_reminder.signin_btn', compact('button_texts'))->render();

                $emailTemplate
                        ->set_student_name($student->getfullNameForEmail($lang))
                        ->set_date($date)
                        ->set_reserved_classes_section($reserved_classes_section)
                        ->set_reserved_events_section($reserved_events_section)
                        ->set_signin_btn($signin_btn);
                        
                app()->setLocale($old_lang);

                $lineMessageBulderData = [
                    'signinUrl' => route('login'),
                    'reserverdEvents' => $reserverdEvents,
                    'reservedClasses' => $reservedClasses
                ];

                $notificationHelper->setTemplate($emailTemplate);
                $notificationHelper->setlineMessageBulderData($lineMessageBulderData);
                $notificationHelper->send();
            }
        }
    }

    public static function sendNewUserNotification($user, $password)
    {
        $role = $user->get_role();
        if(!($role && $role->send_login_details == 1))
        {
            return;
        }

        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('new_user_notification');
        if($emailTemplate->enable == 1)
        {
            $emailTemplate
                ->set_password($password)
                ->set_verification_url($user->getVerificationURL());

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }

    public static function sendVerifyEmailNotification($user)
    {
        $notificationHelper = new NotificationHelper($user);
        $emailTemplate = $notificationHelper->loadTemplate('verify_email_notification');
        if($emailTemplate->enable == 1)
        {
            $emailTemplate
                ->set_user_name($user->name)
                ->set_verify_email_link($user->getVerificationURL());

            $notificationHelper->setTemplate($emailTemplate);
            $notificationHelper->send();
        }
    }
}