<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verificationUrl;
    public ?array $credentials;

    public function __construct(User $user, string $verificationUrl, ?array $credentials = null)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->credentials = $credentials;
    }

    public function build(): self
    {
        return $this->subject(__('messages.email_verify_title'))
            ->view('emails.email-verification');
    }
}
