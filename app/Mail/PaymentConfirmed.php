<?php

namespace App\Mail;

use App\Models\ExamRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public ExamRegistration $registration;

    public function __construct(ExamRegistration $registration)
    {
        $this->registration = $registration;
    }

    public function build(): self
    {
        return $this->subject('Paiement confirmé')
            ->view('emails.payment-confirmed');
    }
}
