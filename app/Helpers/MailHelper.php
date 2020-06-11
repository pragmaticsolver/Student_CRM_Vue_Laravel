<?php


namespace App\Helpers;

use App\Jobs\SendMail;
use App\Mail\ReminderEmail;
use App\Settings;

class MailHelper 
{
    public static function sendMail($to, $content, $subject, $header, $footer)
    {
        SendMail::dispatch($to, $content, $subject, $header, $footer)->onQueue('emails');
    }

    public static function getSmtpConfigFromDb()
    {
        $smtp_settings = Settings::get_value('smtp_settings');
        $smtp_settings = json_decode($smtp_settings, 1);
        return $smtp_settings;
    }

    public static function sendSMTPEmail($smtp_settings, $email_data)
    {
        $transport = (new \Swift_SmtpTransport($smtp_settings['host'], $smtp_settings['port']))
                ->setEncryption('tls')
                ->setUsername($smtp_settings['username'])
                ->setPassword($smtp_settings['password']);

        $mailer = app(\Illuminate\Mail\Mailer::class);
        $mailer->setSwiftMailer(new \Swift_Mailer($transport));

        $mailer->to($email_data['to'])
            ->send(new ReminderEmail(
                $email_data['content'],
                $email_data['subject'],
                $email_data['header'],
                $email_data['footer'],
                $smtp_settings['from_address'],
                $smtp_settings['from_name']));
    }
}

?>