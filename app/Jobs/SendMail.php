<?php

namespace App\Jobs;

use App\Helpers\MailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $to;
    private $content;
    private $subject;
    private $header;
    private $footer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $content, $subject, $header, $footer)
    {
        $this->to = $to;
        $this->content = $content;
        $this->subject = $subject;
        $this->header = $header;
        $this->footer = $footer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $smtp_settings = MailHelper::getSmtpConfigFromDb();
        $email_data = [
            'to' => $this->to,
            'content' => $this->content,
            'subject' => $this->subject,
            'header' => $this->header,
            'footer' => $this->footer
        ];
        MailHelper::sendSMTPEmail($smtp_settings, $email_data);
    }
}
