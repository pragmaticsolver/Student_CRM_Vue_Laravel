<?php
namespace App\Notifications;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase
{
    public function getVerificationUrl($notifiable)
    {
        return $this->verificationUrl($notifiable);
    }
}
