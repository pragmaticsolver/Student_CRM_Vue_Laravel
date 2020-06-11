<?php

namespace App\Http\Controllers;

use App\EmailTemplates;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Students;

class MailController extends Controller
{
	public function send(Request $request, $id)
    {  
    	$email = $request->get('email');
    	$content = $request->get('message');
    	$subject = $request->get('subject');

		$lang = Students::findOrFail($id)->user->get_lang();
		
		MailHelper::sendMail($email,
				$content,
				$subject,
				EmailTemplates::get_header($lang),
            	EmailTemplates::get_footer($lang)
			);

        return redirect('/student/'.$id)->with('success', 'Send mail successfully!');
	}
	
	public function sendTestEmail(Request $request)
	{
		$smtp_settings = [
			'host' => $request->test_smtp_host,
			'port' => $request->test_smtp_port,
			'username' => $request->test_smtp_username,
			'password' => $request->test_smtp_password,
			'from_address' => $request->test_smtp_from_address,
			'from_name' => $request->test_smtp_from_name
		];

		$lang = \Auth::user()->get_lang();

        $email_data = [
            'to' => $request->email,
            'content' => $request->message,
            'subject' => $request->subject,
            'header' => EmailTemplates::get_header($lang),
            'footer' => EmailTemplates::get_footer($lang)
		];

		try
		{
			MailHelper::sendSMTPEmail($smtp_settings, $email_data);
			
			$out['status'] = 1;
			$out['message'] = __('messages.email-sent-successfully-please-verify-it-in-your-inbox-before-saving-this-form');
			return $out;
		}
		catch(\Exception $e)
		{
			$out['status'] = 0;
			$out['message'] = $e->getMessage();
			return $out;
		}
	}
}
