<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $appName;
    public $expiryMinutes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->appName = Setting::where('name', 'app_name')->value('value') ?? 'News Portal';
        
        // Get expiry minutes from config
        $passwordDefaults = config('auth.defaults.passwords');
        $this->expiryMinutes = config("auth.passwords.{$passwordDefaults}.expire", 60);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password Notification - ' . $this->appName)
            ->view('front_end.emails.password-reset');
    }
}