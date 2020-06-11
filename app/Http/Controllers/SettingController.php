<?php

namespace App\Http\Controllers;

use App\Role;
use App\Schedules;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Settings;
use App\CancellationPolicies;
use App\NotificationText;
use App\EmailTemplates;
use App\Helpers\CommonHelper;
use App\Students;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class SettingController extends Controller
{
    public function school()
    {
    	$default_size = DB::table('settings')->select('name', 'value')->where('name','=','limit_number_of_students_per_class')->get();
        $number_of_days = DB::table('settings')->select('name', 'value')->where('name','=','number_of_days_use_payment_points')->get();
        //$use_points = DB::table('settings')->select('name', 'value')->where('name','=','use_points')->get();
        $use_monthly_payments = DB::table('settings')->select('name', 'value')->where('name','=','use_monthly_payments')->get();
        $cancellation_policies = DB::table('cancellation_policies')->select('cancellation_policies.id','cancellation_policies.cancel_type_id','cancellation_policies.payment_plan_id','cancellation_policies.points','cancellation_policies.salary','cancel_types.name')->join('cancel_types','cancellation_policies.cancel_type_id','=','cancel_types.id')->where('payment_plan_id','=',0)->get();
    	return view('setting.school', array(
    	    'default_size' => $default_size, 'number_of_days' => $number_of_days,
            'cancellation_policies' => $cancellation_policies, 
            //'use_points' => $use_points, 
            'use_monthly_payments' => $use_monthly_payments,
            'class_student_levels' => Settings::get_value('class_student_levels'),
            'default_calendar_view' => Settings::get_value('default_calendar_view'), 'calendar_views' => Schedules::get_calendar_views(),
            'default_calendar_color_coding' => Settings::get_value('default_calendar_color_coding'),
            'default_signup_role' => Settings::get_value('default_signup_role'),
            'default_class_length' => Settings::get_value('default_class_length'),
            'school_name' => Settings::get_value('school_name'),
            'school_initial' => Settings::get_value('school_initial'),
            'roles' => Role::get(),
            'default_lang' => Settings::get_value('default_lang'),
            'google_map_api_key' => Settings::get_value('google_map_api_key'),
            'leftover_class_expiration_period' => Settings::get_value('leftover_class_expiration_period'),
            'student_reminder_email_time' => Settings::get_value('student_reminder_email_time'),
            'student_reminder_email_lesson_types' => explode(',',Settings::get_value('student_reminder_email_lesson_types')),
            'school_timezone' => Settings::get_value('school_timezone'),
            'show_other_teachers_classes' => Settings::get_value('show_other_teachers_classes'),
            'use_zoom' => Settings::get_value('use_zoom'),
            'zoom_api_key' => Settings::get_value('zoom_api_key'),
            'zoom_secret_key' => Settings::get_value('zoom_secret_key'),
            'zoom_email_notification_to' =>  explode(',',Settings::get_value('zoom_email_notification_to')),
            'zoom_email_notification_before' => Settings::get_value('zoom_email_notification_before'),
            'zoom_webhook_verification_token' => Settings::get_value('zoom_webhook_verification_token')
        ));
    }

    public function user()
    {
        return view('setting.user', [
            'calendar_views' => Schedules::get_calendar_views()
        ]);
    }

    public function update(Request $request)
    {
        try {
            $not_use_points = (!$request->has('use_points') || empty($request->get('use_points')));

            if(!$not_use_points)
            {
                Settings::update_value('number_of_days_use_payment_points', $request->number_of_days);

                $cancellation_policies = DB::table('cancellation_policies')->select('cancellation_policies.id')->where('payment_plan_id','=',0)->get();
                if(!$cancellation_policies->isEmpty()) {
                    foreach ($cancellation_policies as $cancellation_policy) {
                        $id = $cancellation_policy->id;
                        $cancellation = CancellationPolicies::find($id);
                        if($request->has('points_'.$id)) {
                            $cancellation->points = $request->get('points_'.$id);
                        }
                        if($request->has('salary_'.$id)) {
                            $cancellation->salary = $request->get('salary_'.$id);
                        }
                        $cancellation->save();
                    }
                }
            }

            Settings::update_value('use_points', $not_use_points ? 'false' : 'true');

            Settings::update_value('limit_number_of_students_per_class', $request->default_size);

            $not_use_monthly_payments = (!$request->has('use_monthly_payments') || empty($request->get('use_monthly_payments')));
            Settings::update_value('use_monthly_payments', $not_use_monthly_payments ? 'false' : 'true');

            Settings::update_value('class_student_levels', $request->class_student_levels);

            Settings::update_value('default_calendar_view', $request->default_calendar_view);
            Settings::update_value('default_calendar_color_coding', $request->default_calendar_color_coding);
            Settings::update_value('default_signup_role', $request->default_signup_role);
            Settings::update_value('default_class_length', $request->default_class_length);
            Settings::update_value('school_name', $request->school_name);
            Settings::update_value('school_initial', $request->school_initial);
            Settings::update_value('default_lang', $request->default_lang);
            Settings::update_value('google_map_api_key', $request->google_map_api_key ? $request->google_map_api_key : '');
            Settings::update_value('leftover_class_expiration_period', (int)$request->leftover_class_expiration_period);

            Settings::update_value('student_reminder_email_time',$request->student_reminder_email_time);
            Settings::update_value('student_reminder_email_lesson_types', implode(',', (array)$request->student_reminder_email_lesson_types));
            Settings::update_value('school_timezone',$request->school_timezone);

            if($request->remove_favicon)
            {
                @Storage::disk('public')->delete('favicon.ico');
            }
            if($request->favicon)
            {
                $file = $request->favicon;
                $new_file_name = 'favicon.ico';
                Storage::disk('public')->putFileAs('', $file, $new_file_name);
            }
            
            Settings::update_value('show_other_teachers_classes', $request->show_other_teachers_classes ? 1 : 0);

            $use_zoom = $request->use_zoom ? 1 : 0;
            Settings::update_value('use_zoom', $use_zoom);
            Settings::update_value('zoom_api_key', $use_zoom ? $request->zoom_api_key : '');
            Settings::update_value('zoom_secret_key', $use_zoom ? $request->zoom_secret_key : '');
            Settings::update_value('zoom_email_notification_to', $use_zoom ? implode(',', (array)$request->zoom_email_notification_to) : '');
            Settings::update_value('zoom_email_notification_before', $use_zoom ? $request->zoom_email_notification_before : NULL);
            Settings::update_value('zoom_webhook_verification_token', $use_zoom ? $request->zoom_webhook_verification_token : NULL);            

            return redirect('school-settings')->with('success', 'Settings has been updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function userupdate(Request $request)
    {
        try {
            $user = User::findOrFail($request->get('user_id'));

            $rules = [];
            $rules['username'] = 'required|alpha_dash|unique:users,username,'.$user->id.'id';

            if(!Auth::user()->willUseParentEmail())
            {
                $rules['email'] = 'required';
            }

            if(!empty($request->get('password')))
            {
                $rules['password'] = 'current_password';
                $rules['newpassword'] = 'min:6';
            }

            $messages = [
                'newpassword.min' => 'New password must be at least :min characters.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            $validator->setCustomMessages($messages);

            if($validator->fails()) {
                return redirect('user-settings')->withErrors($validator)->withInput();
            }

            $user->username = $request->username;
            $user->lang = $request->get('lang');

            if(!empty($request->get('email')))
            {
                $user->email = $request->get('email');
            }

            if(!empty($request->get('password'))) {
                $user->password = bcrypt($request->get('newpassword'));
            }
            if($request->get('receive_emails') == true) {
                $user->receive_emails = 1;
            } else {
                $user->receive_emails = 0;
            }
            $user->receive_line_messsges = $request->receive_line_messsges ? 1 : 0;
            $user->calendar_view = $request->calendar_view;

            $user->save();
            return redirect('user-settings')->with('success', 'Settings has been updated!')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function library()
    {
        return view('setting.library', [
            'expected_checkin_days' => Settings::get_value('library_expected_checkin_days'),
            'book_levels' => Settings::get_value('library_book_levels')
        ]);
    }

    public function library_update(Request $request)
    {
        $request->validate([
            'expected_checkin_days' => 'required|min:0|numeric',
            'book_levels' => 'required'
        ]);

        try {
            Settings::update_value('library_expected_checkin_days', $request->expected_checkin_days);
            Settings::update_value('library_book_levels', $request->book_levels);

            return redirect()->back()->with('success', __('messages.updatelibrarysettings-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function paymentSettings()
    {
        return view('setting.payment-settings', array(
            'stripe_publishable_key' => Settings::get_value('stripe_publishable_key'),
            'stripe_secret_key' => Settings::get_value('stripe_secret_key'),
            'stripe_webhook_signing_secret_key' => Settings::get_value('stripe_webhook_signing_secret_key'),
            'payment_categories' => Settings::get_value('payment_categories'),
            'payment_methods' => Settings::get_value('payment_methods'),
            'roles' => Role::get_student_roles(),
            'generate_payment_info_for_roles' => explode(",", Settings::get_value('generate_payment_info_for_roles')),
            'use_stripe' => Settings::get_value('use_stripe'),
        ));
    }

    public function savePaymentSettings(Request $request)
    {
        try {
            Settings::update_value('payment_categories', $request->payment_categories);
            Settings::update_value('payment_methods', $request->payment_methods);
            
            $generate_payment_info_for_roles = (array)$request->generate_payment_info_for_roles;
            Settings::update_value('generate_payment_info_for_roles', implode(",",$generate_payment_info_for_roles));

            $use_stripe = $request->use_stripe ? 1 : 0;
            Settings::update_value('use_stripe', $use_stripe);
            if($use_stripe)
            {
                Settings::update_value('stripe_publishable_key', $request->stripe_publishable_key);
                Settings::update_value('stripe_secret_key', $request->stripe_secret_key);
                Settings::update_value('stripe_webhook_signing_secret_key', $request->stripe_webhook_signing_secret_key);
            }

            return redirect(route('payment-settings.edit'))->with('success', __('messages.payment-settings-updated-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function scheduleSettings()
    {
        return view('setting.schedule-settings', array(
            'working_days' => explode(',', Settings::get_value('working_days')),
            'week_start_day' => Settings::get_value('week_start_day'),
            'default_show_calendar' => explode(';', Settings::get_value('default_show_calendar')),
        ));
    }

    public function saveScheduleSettings(Request $request)
    {
        try {
            if(!$request->working_days)
            {
                throw new \Exception(__('messages.atleast-one-working-day-is-required-to-be-selected'));
            }

            $working_days = $request->working_days;
            Settings::update_value('working_days', implode(',', $working_days));
            Settings::update_value('week_start_day', $request->week_start_day);
            Settings::update_value('default_show_calendar', implode(';', $request->default_show_calendar));

            return redirect(route('schedule-settings.edit'))->with('success', __('messages.schdeule-settings-saved-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function lesson()
    {
        return view('setting.lesson-settings', array(
            'lesson_description' => Settings::get_value('lesson_description'),
            'lesson_objectives' => Settings::get_value('lesson_objectives'),
            'lesson_fulltext' => Settings::get_value('lesson_fulltext'),
            'lesson_thumbnail' => Settings::get_value('lesson_thumbnail'),
            'lesson_video' => Settings::get_value('lesson_video'),
            'student_lesson_prep' => Settings::get_value('student_lesson_prep'),
            'vocab_list' => Settings::get_value('vocab_list'),
            'extra_materials_text' => Settings::get_value('extra_materials_text'),
            'lesson_teachers_notes' => Settings::get_value('lesson_teachers_notes'),
            'lesson_teachers_prep' => Settings::get_value('lesson_teachers_prep'),
            'exercises' => Settings::get_value('exercises'),
            'homework' => Settings::get_value('homework'),
            'downloadable_files' => Settings::get_value('downloadable_files'),
            'pdf_files' => Settings::get_value('pdf_files'),
            'audio_files' => Settings::get_value('audio_files'),
            'lesson_description_required' => Settings::get_value('lesson_description_required'),
            'lesson_objectives_required' => Settings::get_value('lesson_objectives_required'),
            'lesson_fulltext_required' => Settings::get_value('lesson_fulltext_required'),
            'lesson_thumbnail_required' => Settings::get_value('lesson_thumbnail_required'),
            'lesson_video_required' => Settings::get_value('lesson_video_required'),
            'student_lesson_prep_required' => Settings::get_value('student_lesson_prep_required'),
            'vocab_list_required' => Settings::get_value('vocab_list_required'),
            'extra_materials_text_required' => Settings::get_value('extra_materials_text_required'),
            'lesson_teachers_notes_required' => Settings::get_value('lesson_teachers_notes_required'),
            'lesson_teachers_prep_required' => Settings::get_value('lesson_teachers_prep_required'),
            'exercises_required' => Settings::get_value('exercises_required'),
            'homework_required' => Settings::get_value('homework_required'),
            'downloadable_files_required' => Settings::get_value('downloadable_files_required'),
            'pdf_files_required' => Settings::get_value('pdf_files_required'),
            'audio_files_required' => Settings::get_value('audio_files_required'),
        ));
    }

    public function lessonupdate(Request $request)
    {
        try {
            Settings::update_value('lesson_description', $request->lesson_description);
            Settings::update_value('lesson_objectives', $request->lesson_objectives);
            Settings::update_value('lesson_fulltext', $request->lesson_fulltext);
            Settings::update_value('lesson_thumbnail', $request->lesson_thumbnail);
            Settings::update_value('lesson_video', $request->lesson_video);
            Settings::update_value('student_lesson_prep', $request->student_lesson_prep);
            Settings::update_value('vocab_list', $request->vocab_list);
            Settings::update_value('extra_materials_text', $request->extra_materials_text);
            Settings::update_value('lesson_teachers_notes', $request->lesson_teachers_notes);
            Settings::update_value('lesson_teachers_prep', $request->lesson_teachers_prep);
            Settings::update_value('exercises', $request->exercises);
            Settings::update_value('homework', $request->homework);
            Settings::update_value('downloadable_files', $request->downloadable_files);
            Settings::update_value('pdf_files', $request->pdf_files);
            Settings::update_value('audio_files', $request->audio_files);
         
            Settings::update_value('lesson_description_required', $request->lesson_description_required);
            Settings::update_value('lesson_objectives_required', $request->lesson_objectives_required);
            Settings::update_value('lesson_fulltext_required', $request->lesson_fulltext_required);
            Settings::update_value('lesson_thumbnail_required', $request->lesson_thumbnail_required);
            Settings::update_value('lesson_video_required', $request->lesson_video_required);
            Settings::update_value('student_lesson_prep_required', $request->student_lesson_prep_required);
            Settings::update_value('vocab_list_required', $request->vocab_list_required);
            Settings::update_value('extra_materials_text_required', $request->extra_materials_text_required);
            Settings::update_value('lesson_teachers_notes_required', $request->lesson_teachers_notes_required);
            Settings::update_value('lesson_teachers_prep_required', $request->lesson_teachers_prep_required);
            Settings::update_value('exercises_required', $request->exercises_required);
            Settings::update_value('homework_required', $request->homework_required);
            Settings::update_value('downloadable_files_required', $request->downloadable_files_required);
            Settings::update_value('pdf_files_required', $request->pdf_files_required);
            Settings::update_value('audio_files_required', $request->audio_files_required);
         
            return redirect(route('lesson-settings.edit'))->with('success', __('messages.lesson-settings-saved-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function lineSettings()
    {
        return view('setting.line-settings', array(
            'use_line_messaging_api' => Settings::get_value('use_line_messaging_api'),
            'line_channel_id' => Settings::get_value('line_channel_id'),
            'line_channel_secret' => Settings::get_value('line_channel_secret'),
            'line_assertion_private_key' => Settings::get_value('line_assertion_private_key'),
            'line_add_friend_button_html' => Settings::get_value('line_add_friend_button_html'),
            'line_account_link_meesage_text' => Settings::get_value('line_account_link_meesage_text'),
            'line_account_link_meesage_button_text' => Settings::get_value('line_account_link_meesage_button_text'),
            'line_account_linked_message_text_en' => Settings::get_value('line_account_linked_message_text_en'),
            'line_account_linked_message_text_ja' => Settings::get_value('line_account_linked_message_text_ja'),
            
            'use_login_with_line' => Settings::get_value('use_login_with_line'),
            'line_login_channel_id' => Settings::get_value('line_login_channel_id'),
            'line_login_channel_secret' => Settings::get_value('line_login_channel_secret'),
        ));
    }

    public function saveLineLoginSettings(Request $request)
    {
        if($request->use_login_with_line)
        {
            $request->validate([
                'line_login_channel_id' => 'required',
                'line_login_channel_secret' => 'required',
            ]);
        }
        
        Settings::update_value('use_login_with_line', $request->use_login_with_line ? 1 : 0);
        Settings::update_value('line_login_channel_id', $request->line_login_channel_id);
        Settings::update_value('line_login_channel_secret', $request->line_login_channel_secret);

        return [
            'status' => 1,
            'message' => __('messages.settings-saved-successfully')
        ];
    }

    public function saveLineMessagingSettings(Request $request)
    {
        if($request->use_line_messaging_api)
        {
            $request->validate([
                'line_channel_id' => 'required',
                'line_channel_secret' => 'required',
                'line_assertion_private_key' => 'required',
                'line_account_link_meesage_text' => 'required|max:160',
                'line_account_link_meesage_button_text' => 'required|max:20',
                'line_account_linked_message_text_en' => 'required|max:2000',
                'line_account_linked_message_text_ja' => 'required|max:2000'
            ]);
        }
        
        Settings::update_value('use_line_messaging_api', $request->use_line_messaging_api ? 1 : 0);
        Settings::update_value('line_channel_id', $request->line_channel_id);
        Settings::update_value('line_channel_secret', $request->line_channel_secret);
        Settings::update_value('line_assertion_private_key', $request->line_assertion_private_key);

        Settings::update_value('line_account_link_meesage_text', $request->line_account_link_meesage_text);
        Settings::update_value('line_account_link_meesage_button_text', $request->line_account_link_meesage_button_text);
        Settings::update_value('line_account_linked_message_text_en', $request->line_account_linked_message_text_en);
        Settings::update_value('line_account_linked_message_text_ja', $request->line_account_linked_message_text_ja);

        Settings::update_value('line_add_friend_button_html', $request->line_add_friend_button_html);

        return [
            'status' => 1,
            'message' => __('messages.settings-saved-successfully')
        ];
    }

    public function notificationSettings()
    {
        $email_templates = [];

        $email_template_data = EmailTemplates::getTemplateLocalData();
        foreach($email_template_data as $key => $email_temp)
        {
            $temp = $email_temp;
            $temp['db'] = EmailTemplates::get_by_name($key);
            $email_templates[$key] = $temp;
        }
        
        $data['email_templates'] = $email_templates;
        $data['email_templates_global_parameters'] = EmailTemplates::getGlobalParamters();
        $data['students'] = Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get();
        $data['now'] = Carbon::now(CommonHelper::getSchoolTimezone());
        $data['email_header_footer_color'] = Settings::get_value('email_header_footer_color');
        $data['email_header_image'] = Settings::get_value('email_header_image');
        $data['email_header_text_size'] = Settings::get_value('email_header_text_size');
        $data['email_body_text_size'] = Settings::get_value('email_body_text_size');
        $data['email_header_text_en'] = Settings::get_value('email_header_text_en');
        $data['email_header_text_ja'] = Settings::get_value('email_header_text_ja');
        $data['email_footer_text_en'] = Settings::get_value('email_footer_text_en');
        $data['email_footer_text_ja'] = Settings::get_value('email_footer_text_ja');

        $smtp_settings = Settings::get_value('smtp_settings');
        $data['smtp_settings'] = $smtp_settings ? json_decode($smtp_settings,1) : [];
        $data['use_line_messaging_api'] = Settings::get_value('use_line_messaging_api');

        $data['email_lesson_types'] = explode(',', Settings::get_value('student_reminder_email_lesson_types'));
        $data['global_parameters_available_to_use_message'] = "<p>".__('messages.global-parameters-available-to-use').":  <b>{username}</b></p>";
        return view('setting.notification-settings', $data);
    }

    public function saveNotificationStatus(Request $request)
    {
        $email_templates = EmailTemplates::all();
        foreach($email_templates as $email_template) 
        {
            if($request->enable && in_array($email_template->name, $request->enable)) 
            {
                $email_template->enable = EmailTemplates::ENABLE_STATUS;
            }
            else 
            {
                $email_template->enable = EmailTemplates::DISABLE_STATUS;
            }

            $email_template->save();
        }

        return [
            'status' => 1,
            'message' => __('messages.settings-saved-successfully')
        ];
    }

    public function saveNotificationText(Request $request)
    {
        $email_template_name = $request->name;
        if ($email_template_name == 'daily_reservation_reminder') {
            Settings::update_value('event_reminder', $request->event_reminder);
            Settings::update_value('class_reminder', $request->class_reminder);
        }
        $use_line_messaging_api = Settings::get_value('use_line_messaging_api');
        $emailTemplate = EmailTemplates::get_by_name($email_template_name);
        $emailTemplateLocalData = EmailTemplates::getTemplateLocalData($email_template_name);

        $buttonTexts = $emailTemplate->buttonTexts;

        // Validation
        $validationParams = [
                'subject_en' => 'required|max:191',
                'content_en' => 'required',
                'subject_ja' => 'required|max:191',
                'content_ja' => 'required',
        ];
        foreach($buttonTexts as $buttonText) {
            $params = $buttonText->getValidationParams();
            $validationParams = array_merge($validationParams, $params);
        }

        if($use_line_messaging_api)
        {
            if($emailTemplateLocalData['line_message_type'])
            {
                foreach($emailTemplateLocalData['line_text_fields'] as $lineTextField)
                {
                    $validationParams[$lineTextField['name'].'_en'] = 'required|max:'. $lineTextField['max_length'];
                    $validationParams[$lineTextField['name'].'_ja'] = 'required|max:'. $lineTextField['max_length'];
                }
            }
        }
        
        $request->validate($validationParams);

        // Save Data
        $emailTemplate->subject_en = $request->subject_en;
        $emailTemplate->content_en = $request->content_en;
        $emailTemplate->subject_ja = $request->subject_ja;
        $emailTemplate->content_ja = $request->content_ja;
        $emailTemplate->save();

        if($request->button_text)
        {
            foreach($request->button_text as $button_text_id => $val)
            {
                NotificationText::where('id', $button_text_id)->update($val);
            }
        }

        if($use_line_messaging_api)
        {
            if($emailTemplateLocalData['line_message_type'])
            {
                foreach($emailTemplateLocalData['line_text_fields'] as $lineTextField) {

                    $notificationText = NotificationText::firstOrNew([
                        'email_template_id' => $emailTemplate->id,
                        'type' => NotificationText::TYPE_LINE_TEXT,
                        'key' => $lineTextField['name']
                    ]);
                    $notificationText->text_en = $request[$lineTextField['name'].'_en'];
                    $notificationText->text_ja = $request[$lineTextField['name'].'_ja'];
                    $notificationText->save();
                }
            }
        }

        return [
            'status' => 1,
            'message' => __('messages.settings-saved-successfully')
        ];
    }

    public function terminalSettings()
    {
        return view('setting.terminal-settings', array(
            'terminal_checkin' => Settings::get_value('terminal_checkin'),
            'terminal_checkout' => Settings::get_value('terminal_checkout'),
            'terminal_reservation' => Settings::get_value('terminal_reservation'),
            'terminal_checkout_book' => Settings::get_value('terminal_checkout_book'),
        ));
    }

    public function terminalSettingsUpdate(Request $request)
    {
        try {
            Settings::update_value('terminal_checkin', $request->terminal_checkin);
            Settings::update_value('terminal_checkout', $request->terminal_checkout);
            Settings::update_value('terminal_reservation', $request->terminal_reservation);
            Settings::update_value('terminal_checkout_book', $request->terminal_checkout_book);
           
            return redirect(route('terminal-settings.edit'))->with('success', __('messages.terminal-settings-saved-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
