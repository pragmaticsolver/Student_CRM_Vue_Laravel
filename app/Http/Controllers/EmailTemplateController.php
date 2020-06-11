<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DRREmail;
use App\Settings;
use App\Students;
use App\Mail\ReminderEmail;
use Illuminate\Support\Facades\Storage;

class EmailTemplateController extends Controller
{
    public function get() 
    {
        $data = [];
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
        return view('setting.email-templates', $data);
    }

    public function update(Request $request)
    {
        try {
            $update_header_image = 0;
            if($request->remove_email_header_image)
            {
                $header_image = NULL;
                @Storage::disk('public')->delete(Settings::get_value('email_header_image'));
                $update_header_image = 1;
            }
            if($request->email_header_image)
            {
                $file = $request->email_header_image;
                $new_file_name = 'email_header_image.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $header_image = Storage::disk('public')->putFileAs('', $file, $new_file_name);
                $update_header_image = 1;
            }

            if($update_header_image)
            {
                Settings::update_value('email_header_image', $header_image);
            }

            Settings::update_value('email_header_footer_color', $request->email_header_footer_color);

            Settings::update_value('email_header_text_size', (string)$request->email_header_text_size);
            Settings::update_value('email_body_text_size', (string)$request->email_body_text_size);
            Settings::update_value('email_header_text_en', (string)$request->email_header_text_en);
            Settings::update_value('email_header_text_ja', (string)$request->email_header_text_ja);
            Settings::update_value('email_footer_text_en', (string)$request->email_footer_text_en);
            Settings::update_value('email_footer_text_ja', (string)$request->email_footer_text_ja);

            $smtp_settings = [
                'host' => $request->smtp_host,
                'port' => $request->smtp_port,
                'username' => $request->smtp_username,
                'password' => $request->smtp_password,
                'from_address' => $request->smtp_from_address,
                'from_name' => $request->smtp_from_name
            ];
            Settings::update_value('smtp_settings', json_encode($smtp_settings));
            
        } catch(\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect(route('email-settings.edit'))->with('success', __('messages.email-settings-saved-sucessfully'));
    }

    public function preview_drr_email(Request $request)
    {
        $lang = $request->lang;

        $drrmail = new DRREmail();
        $drrmail->set_date($request->date);
        $drrmail->set_student(Students::find($request->student_id));
        $drrmail->set_lang($lang);
        if (!empty($request->class_reminder))
            $drrmail->set_class_reminder($request->class_reminder);
        if (!empty($request->event_reminder))
            $drrmail->set_event_reminder($request->event_reminder);
        
        $email_data = $drrmail->get_email_data();
        if($email_data['subject'])
        {
            return new ReminderEmail(
                $email_data['content'],
                $email_data['subject'],
                $email_data['header'],
                $email_data['footer'],
                '',
                ''
            );
        }
        else
        {
            return __('messages.insufficient-data-to-generate-email');
        }
    }
}
