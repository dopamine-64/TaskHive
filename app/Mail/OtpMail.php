<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        $html = "<!DOCTYPE html>
        <html>
        <head><title>OTP Verification</title></head>
        <body>
            <h2>TaskHive Verification</h2>
            <p>Your OTP code is: <strong>{$this->code}</strong></p>
            <p>This code expires in 10 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </body>
        </html>";
        
        return $this->from(env('MAIL_FROM_ADDRESS'), 'TaskHive')
                    ->subject('Your OTP for TaskHive Registration')
                    ->html($html);
    }
}