<?php

namespace App\Mail;

use App\Helpers\CommonHelper;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $subject;
    public $header_text;
    public $footer_text;

    public $header_text_size;
    public $body_text_size;
    public $header_footer_color;
    public $header_image;

    public $from_address;
    public $from_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $subject, $header, $footer, $from_address, $from_name)
    {
        $this->content = $content;
        $this->subject = $subject;
        $this->header_text = $header;
        $this->footer_text = $footer;

        $this->from_address = $from_address;
        $this->from_name = $from_name;

        $this->header_text_size = Settings::get_value('email_header_text_size')."px";
        $this->body_text_size = Settings::get_value('email_body_text_size')."px";
        $this->header_footer_color = Settings::get_value('email_header_footer_color');
        $this->header_image = NULL;

        $email_header_image = Settings::get_value('email_header_image');
        if($email_header_image)
        {
            $this->header_image = Storage::disk('public')->url($email_header_image);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->from_address, $this->from_name)
                    ->view('layouts.email')
                    ->with([
                        'content' => $this->content,
                    ])
                    ->subject($this->subject);
    }
}
