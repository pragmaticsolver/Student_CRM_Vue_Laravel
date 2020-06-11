<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class EmailTemplates extends Model
{
    protected $table = 'email_templates';
    
    public const ENABLE_STATUS = 1;
    public const DISABLE_STATUS = 0;

    private $date = '';
    private $time = '';
    private $class_weekday = '';
    private $class_name = '';
    private $student_name = '';
    private $teacher_name = '';
    private $username = '';
    private $password = '';
    private $verification_url = '';
    private $course = '';
    private $unit = '';
    private $lesson = '';
    private $test = '';
    private $test_url = '';
    private $assessment = '';
    private $assessment_url = '';
    private $user_name = '';
    private $date_time = '';
    private $reserved_classes_section = '';
    private $reserved_events_section = '';
    private $signin_btn = '';
    private $view_assessment_url = '';
    private $action_btns = '';
    private $reset_password_link = '';
    private $verify_email_link = '';
    private $zoom_meeting_url = '';
    private $zoom_meeting_id = '';
    private $zoom_meeting_password = '';
    private $reserve_btn_url = '';
    private $cancel_waitlist_btn_url = '';
    private $format_fields = [
        'date', 'time', 'class_weekday', 'class_name', 'student_name', 'teacher_name',
        'username', 'password', 'verification_url', 'course', 'unit',
        'lesson', 'test', 'test_url', 'assessment', 'assessment_url', 'user_name', 'date_time',
        'reserved_classes_section', 'reserved_events_section', 'signin_btn', 'view_assessment_url', 'action_btns',
        'reset_password_link', 'verify_email_link', 'zoom_meeting_url', 'zoom_meeting_id', 'zoom_meeting_password',
        'reserve_btn_url', 'cancel_waitlist_btn_url'
    ];
    
    public static function getGlobalParamters()
    {
        return [
            ['name' => 'username']
        ];
    }

    public static function getTemplateLocalData($name = NULL)
    {
        $data = [
            'register_class_notification_repeat' => [
                'title' =>  __('messages.registerclassnotification'),
                'template_variables' => [
                    ['name' => 'student_name'],
                    ['name' => 'class_name' ],
                    ['name' => 'date', 'info' => __('messages.date-may-contain-comma-seprated-values')],
                    ['name' => 'time' ],
                ],
                'usage' => __('messages.sent-to-student-when-reservation-is-made-for-any-class'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'register_class_notification_to_teacher' => [
                'title' => __('messages.register-class-notifiction-[-to-teacher-]'),
                'template_variables' => [
                    ['name' => 'teacher_name'],
                    ['name' => 'student_name'],
                    ['name' => 'class_name' ],
                    ['name' => 'date', 'info' => __('messages.date-may-contain-comma-seprated-values')],
                    ['name' => 'time' ]
                ],
                'usage' => __('messages.sent-to-teacher-when-reservation-is-made-for-any-of-their-classes'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'waitlist_class_notification' => [
                'title' =>  __('messages.waitlist-class-notification'),
                'template_variables' => [
                    ['name' => 'student_name'],
                    ['name' => 'class_name' ],
                    ['name' => 'date' ],
                    ['name' => 'time' ],
                ],
                'usage' => __('messages.sent-to-student-when-they-are-added-to-waitlist-for-particular-class'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'cancel_class_notification' => [
                'title' => __('messages.cancel-class-notification'),
                'template_variables' => [
                    ['name' => 'student_name'],
                    ['name' => 'class_name' ],
                    ['name' => 'date', 'info' => __('messages.date-may-contain-comma-seprated-values')],
                    ['name' => 'time' ],
                ],
                'usage' => __('messages.sent-to-student-when-reservation-is-cancelled-for-any-class'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'cancel_reservation_notify_waitlist' => [
                'title' => __('messages.cancelreservationnotifywaitlist'),
                'template_variables' => [
                    ['name' => 'student_name'],
                    ['name' => 'class_name'],
                    ['name' => 'date'],
                    ['name' => 'action_btns'],
                    ['name' => 'reserve_btn_url'],
                    ['name' => 'cancel_waitlist_btn_url']
                ],
                'usage' => __('messages.when-reservation-is-cancelled-for-particular-class,-notify-other-waitlisted-students-about-this-cancellation-so-they-can-make-reservation'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'reserve_button_text',
                        'max_length' => 20,
                        'value_variable' => 'reserve_btn_url',
                    ],
                    [
                        'name' => 'cancel_waitlist_button_text',
                        'max_length' => 20,
                        'value_variable' => 'cancel_waitlist_btn_url'
                    ]
                ]
            ],
            'new_user_notification' => [
                'title' => __('messages.newusernotification'),
                'template_variables' => [
                    ['name' => 'username'],
                    ['name' => 'password'],
                    ['name' => 'verification_url'],
                ],
                'usage' => __('messages.sent-to-newly-created/registered-user'),
                'line_message_type' => NULL
            ],
            'test_notification' => [
                'title' => __('messages.testnotification'),
                'template_variables' => [
                    ['name' => 'student_name' ],
                    ['name' => 'course' ],
                    ['name' => 'lesson' ],
                    ['name' => 'unit' ],
                    ['name' => 'date' ],
                    ['name' => 'test' ],
                    ['name' => 'test_url' ],
                ],
                'usage' => __('messages.when-lesson-is-marked-as-complete-form-course-progress-page,-test-(online-test)-is-automatically-assigned-to-students-and-this-email-will-be-sent-to-notify-students-about-the-same'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'test_button_text',
                        'max_length' => 20,
                        'value_variable' => 'test_url',
                    ]
                ]
            ],
            'paper_test_notification' => [
                'title' => __('messages.papertestnotification'),
                'template_variables' => [
                    ['name' => 'teacher_name' ],
                    ['name' => 'course' ],
                    ['name' => 'lesson' ],
                    ['name' => 'unit' ],
                    ['name' => 'date' ],
                    ['name' => 'test' ],
                    ['name' => 'test_url' ],
                ],
                'usage' => __('messages.when-lesson-is-marked-as-complete-form-course-progress-page,-teacher-will-be-notified-to-create-paper-tests-for-completed-lesson'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'test_button_text',
                        'max_length' => 20,
                        'value_variable' => 'test_url',
                    ]
                ]
            ],
            'automatic_assessment_notification' => [
                'title' => __('messages.automatic-assessment-notification'),
                'template_variables' => [
                    ['name' => 'user_name' ],
                    ['name' => 'course' ],
                    ['name' => 'lesson' ],
                    ['name' => 'unit' ],
                    ['name' => 'date' ],
                    ['name' => 'assessment' ],
                    ['name' => 'assessment_url' ]
                ],
                'usage' => __('messages.when-lesson-is-marked-as-complete-from-course-progress-page,-automatic-assessments-will-be-assigned-to-students-or-teacher,-this-email-will-be-sent-to-notify-them-about-the-same'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'assessment_button_text',
                        'max_length' => 20,
                        'value_variable' => 'assessment_url',
                    ]
                ]
            ],
            'manual_assessment_notification' => [
                'title' => __('messages.manual-assessment-notification'),
                'template_variables' => [
                    ['name' => 'user_name'],
                    ['name' => 'assessment'],
                    ['name' => 'assessment_url']
                ],
                'usage' => __('messages.this-email-will-be-sent-to-students-or-teacher-when-an-assessment-is-being-assigned-to-them-either-from-schedule-details-page-or-from-assessment-list-page'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'assessment_button_text',
                        'max_length' => 20,
                        'value_variable' => 'assessment_url',
                    ]
                ]
            ],
            'checkin_notification' => [
                'title' =>__('messages.checkin-notification'),
                'template_variables' => [
                    ['name' => 'student_name' ],
                    ['name' => 'date_time' ],
                ],
                'usage' => __('messages.this-email-will-be-sent-to-students-when-they-do-checkin-via-terminal'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'checkout_notification' => [
                'title' => __('messages.checkout-notification'),
                'template_variables' => [
                    ['name' => 'student_name' ],
                    ['name' => 'date_time' ],
                ],
                'usage' => __('messages.this-email-will-be-sent-to-students-when-they-do-checkout-via-terminal'),
                'line_message_type' => 'text',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 2000
                    ]
                ]
            ],
            'daily_reservation_reminder' => [
                'title' => __('messages.daily-reservation-reminder'),
                'template_variables' => [
                    ['name' => 'student_name'] ,
                    ['name' => 'date'] ,
                    ['name' => 'reserved_classes_section'] ,
                    ['name' => 'reserved_events_section'] ,
                    ['name' => 'signin_btn'] ,
                ],
                'usage' => __('messages.this-email-will-be-sent-to-students-daily-(at-\'student-reminder-email-time\'-defined-in-school-settings-page)-to-remind-about-their-today\'s-reservations'),
                'line_message_type' => 'carousel-template',
                'line_text_fields' => [
                    [
                        'name' => 'first_column_text',
                        'max_length' => 120
                    ],
                    [
                        'name' => 'signin_button_text',
                        'max_length' => 20
                    ],
                    [
                        'name' => 'cancel_class_button_text',
                        'max_length' => 20
                    ],
                    [
                        'name' => 'cancel_event_button_text',
                        'max_length' => 20
                    ]
                ]
            ],
            'assessment_result_available_notification' => [
                'title' => __('messages.assessment-result-available-notification'),
                'template_variables' => [
                    ['name' => 'student_name'],
                    ['name' => 'class_name'],
                    ['name' => 'view_assessment_url'],
                ],
                'usage' => __('messages.when-teacher-submits-(completes)-assessment-for-a-student,-this-email-will-be-sent-to-student'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'assessment_button_text',
                        'max_length' => 20,
                        'value_variable' => 'view_assessment_url',
                    ]
                ]
            ],
            'password_reset_link_notification' => [
                'title' => __('messages.password-reset-link-notification'),
                'template_variables' => [
                    ['name' => 'user_name'],
                    ['name' => 'reset_password_link'],
                ],
                'usage' => __('messages.whenever-user-forget-their-password,-a-link-to-reset-their-password-will-be-sent-by-this-email-upon-request'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'reset_password_button_text',
                        'max_length' => 20,
                        'value_variable' => 'reset_password_link',
                    ]
                ],
            ],
            'verify_email_notification' => [
                'title' => __('messages.verify-email-notification'),
                'template_variables' => [
                    ['name' => 'user_name'],
                    ['name' => 'verify_email_link'],
                ],
                'usage' => __('messages.this-email-will-be-sent-to-user-when-they-request-a-link-to-verify-their-email-when-they-login-initially,-or-whenever-email-verification-is-forced-to-be-reconfirmed-from-admin-side'),
                'line_message_type' => NULL
            ],
            'zoom_meeting_reminder_for_class' => [
                'title' => __('messages.zoom-meeting-reminder-[-for-class-]'),
                'template_variables' => [
                    ['name' => 'class_name' ],
                    ['name' => 'date' ],
                    ['name' => 'time' ],
                    ['name' => 'zoom_meeting_url'],
                    ['name' => 'zoom_meeting_id'],
                    ['name' => 'zoom_meeting_password'],
                ],
                'usage' => __('messages.sent-,o-student-and/or-teacher-to-remind-them-about-zoom-meeting-for-class'),
                'line_message_type' => 'button-template',
                'line_text_fields' => [
                    [
                        'name' => 'message_text',
                        'max_length' => 160
                    ],
                    [
                        'name' => 'zoom_meeting_button_text',
                        'max_length' => 20,
                        'value_variable' => 'zoom_meeting_url',
                    ]
                ]
            ],
        ];

        if($name) 
        {
            return $data[$name];
        }
        else
        {
            return $data;
        }
    }

    public $timestamps = false;

    protected $fillable = [
        'name',
        'subject_en',
        'content_en',
        'subject_jp',
        'content_jp'
    ];

    public function buttonTexts()
    {
        return $this->hasMany('App\NotificationText', 'email_template_id', 'id')->where('type', NotificationText::TYPE_BUTTON_TEXT);
    }

    public function lineTexts() 
    {
        return $this->hasMany('App\NotificationText', 'email_template_id', 'id')->where('type', NotificationText::TYPE_LINE_TEXT);
    }

    public function buttonTextsByLang($lang)
    {
        $button_texts = [];
        foreach($this->buttonTexts as $record){
            $button_texts[$record->key] = $record['text_'.$lang];
        }
        return $button_texts;
    }

    public static function get_by_name($name)
    {
        $email_templates = self::where('name', $name);
        if($email_templates->count() <= 0) throw new \Exception(__('messages.emptyemailtemplate', ['email_template' => $name]));

        return $email_templates->first();
    }

    public function is_enable()
    {
        return $this->enable == 1 ? true : false;
    }


    public function set_class_name($name = '')
    {
        $this->class_name = $name;
        return $this;
    }

    public function set_student_name($name = '')
    {
        $this->student_name = $name;
        return $this;
    }

    public function set_date($date = '')
    {
        $this->date = $date;
        return $this;
    }

    public function set_time($time = '')
    {
        $this->time = $time;
        return $this;
    }

    public function set_weekday($weekday = '')
    {
        $this->class_weekday = $weekday;
        return $this;
    }

    public function set_username($username = '')
    {
        $this->username = $username;
        return $this;
    }

    public function set_password($password = '')
    {
        $this->password = $password;
        return $this;
    }

    public function set_verification_url($verification_url = '')
    {
        $this->verification_url = $verification_url;
        return $this;
    }

    public function set_course($course = '')
    {
        $this->course = $course;
        return $this;
    }

    public function set_unit($unit = '')
    {
        $this->unit = $unit;
        return $this;
    }

    public function set_lesson($lesson = '')
    {
        $this->lesson = $lesson;
        return $this;
    }

    public function set_test($test = '')
    {
        $this->test = $test;
        return $this;
    }

    public function set_test_url($test_url = '')
    {
        $this->test_url = $test_url;
        return $this;
    }

    public function set_assessment($assessment = '')
    {
        $this->assessment = $assessment;
        return $this;
    }

    public function set_assessment_url($assessment_url = '')
    {
        $this->assessment_url = $assessment_url;
        return $this;
    }

    public function set_user_name($user_name = '')
    {
        $this->user_name = $user_name;
        return $this;
    }

    public function set_date_time($date_time = '')
    {
        $this->date_time = $date_time;
        return $this;
    }

    public function set_teacher_name($teacher_name = '')
    {
        $this->teacher_name = $teacher_name;
        return $this;
    }

    public function set_reserved_classes_section($reserved_classes_section = '')
    {
        $this->reserved_classes_section = $reserved_classes_section;
        return $this;
    }

    public function set_reserved_events_section($reserved_events_section = '')
    {
        $this->reserved_events_section = $reserved_events_section;
        return $this;
    }

    public function set_signin_btn($signin_btn = '')
    {
        $this->signin_btn = $signin_btn;
        return $this;
    }

    public function get_format($field)
    {
        $format_content = $this->$field;

        foreach($this->format_fields as $field) {
            if(strpos($this->$field, '{'.$field.'}') != -1) {
                $format_content = str_replace('{'.$field.'}', $this->$field, $format_content);
            }
        }

        return $format_content;
    }

    public static function get_header($lang)
    {
        return Settings::get_value('email_header_text_' . $lang);
    }

    public static function get_footer($lang)
    {
        return Settings::get_value('email_footer_text_' . $lang);
    }

    public function set_view_assessment_url($url = '')
    {
        $this->view_assessment_url = $url;
        return $this;
    }

    public function set_action_btns($action_btns = '')
    {
        $this->action_btns = $action_btns;
        return $this;
    }

    public function set_reset_password_link($link = '')
    {
        $this->reset_password_link = $link;
        return $this;
    }

    public function set_verify_email_link($link = '')
    {
        $this->verify_email_link = $link;
        return $this;
    }

    public function set_zoom_meeting_url($url = '')
    {
        $this->zoom_meeting_url = $url;
        return $this;
    }

    public function set_zoom_meeting_id($id = '')
    {
        $this->zoom_meeting_id = $id;
        return $this;
    }

    public function set_zoom_meeting_password($pass = '')
    {
        $this->zoom_meeting_password = $pass;
        return $this;
    }

    public function set_reserve_btn_url($url = '')
    {
        $this->reserve_btn_url = $url;
        return $this;
    }

    public function set_cancel_waitlist_btn_url($url = '')
    {
        $this->cancel_waitlist_btn_url = $url;
        return $this;
    }

    public function getLineMessageBuilder($lang, $data)
    {
        $email_template_name = $this->name;
        $localTemplateData = $this->getTemplateLocalData($email_template_name);

        if(!$localTemplateData['line_message_type'])
        {
            return null;
        }

        // Fetch line texts from db, inject placholder values and format into key value pairs
        $text_key = 'text_'.$lang;
        $lineTexts = $this->lineTexts()->select('key',$text_key)->get();
        $templateVariables = array_merge($this->getGlobalParamters(),$localTemplateData['template_variables']);
        $lineTexts = $lineTexts->mapWithKeys(function($item) use ($text_key, $templateVariables){
            // inject values of placeholders
            $value = $item[$text_key];
            foreach($templateVariables as $templateVar) 
            {
                $template_var_name = $templateVar['name'];
                if(strpos($value, '{'.$template_var_name.'}') != -1) {
                    $value = str_replace('{'.$template_var_name.'}', $this->$template_var_name, $value);
                }
            }
            return [ $item['key'] => $value ];
        });

        // Return if any value is not enered yet.
        foreach($localTemplateData['line_text_fields'] as $lineTextField)
        {
            if(!isset($lineTexts[$lineTextField['name']]))
            {
                return null;
            }
        }

        // return line message builder object
        if($localTemplateData['line_message_type'] == 'text')
        {
            $messageBuiler = new TextMessageBuilder($lineTexts['message_text']);
            return $messageBuiler;
        } 
        else if ($localTemplateData['line_message_type'] == 'button-template')
        {
            $actionBuilders = [];
            foreach($localTemplateData['line_text_fields'] as $lineTextField)
            {
                if(isset($lineTextField['value_variable']))
                {
                    $var_name = $lineTextField['value_variable'];

                    $l_button_text = $lineTexts[$lineTextField['name']];
                    $l_button_url = $this->$var_name;
                    $actionBuilders[] = new UriTemplateActionBuilder($l_button_text, $l_button_url);
                }
            }

            $messageBuiler = new TemplateMessageBuilder($lineTexts['message_text'], new ButtonTemplateBuilder(null, $lineTexts['message_text'], null, $actionBuilders));
            return $messageBuiler;
        }
        else if ($email_template_name == 'daily_reservation_reminder')
        {
            $carouselColumns = array();

            // First collumn
            $carouselColumns[] = new CarouselColumnTemplateBuilder(null, $lineTexts['first_column_text'], null, [
                new UriTemplateActionBuilder($lineTexts['signin_button_text'], $data['signinUrl']),
            ]);

            // columns for classes
            foreach($data['reservedClasses'] as $record)
            {
                $yoyaku = $record['yoyaku'];
                $zoomMeeting = $record['zoomMeeting'];
                $encryptedId = encrypt($yoyaku->id);

                $content = "Class: ". $yoyaku->schedule->class->title. "\n"
                            . $yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time. "\n";

                $carouselColumns[] = new CarouselColumnTemplateBuilder(null, $content, null, [
                    new UriTemplateActionBuilder($lineTexts['cancel_class_button_text'], route('cancel_reservation',$encryptedId)),
                ]);
            }

            // columns for events
            foreach($data['reserverdEvents'] as $yoyaku)
            {
                $is_all_day_event = $yoyaku->schedule->type == \App\Schedules::EVENT_ALLDAY_TYPE;

                $content = "Event: ". $yoyaku->schedule->class->title. "\n";
                $content .= $is_all_day_event ? __('messages.allday') : $yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time;
                $encryptedId = encrypt($yoyaku->id);

                $carouselColumns[] = new CarouselColumnTemplateBuilder(null, $content, null, [
                    new UriTemplateActionBuilder($lineTexts['cancel_event_button_text'], route('cancel_reservation',$encryptedId)),
                ]);
            }

            $messageBuiler = new TemplateMessageBuilder($lineTexts['first_column_text'], new CarouselTemplateBuilder($carouselColumns));
            return $messageBuiler;
        }
    }
}
