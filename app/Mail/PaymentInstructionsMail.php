<?php

namespace App\Mail;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentInstructionsMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $bank;
    public ?Exam $exam;
    public ?float $amount;
    public array $selectedModules;

    public function __construct(User $user, ?Exam $exam = null, ?float $amount = null, array $selectedModules = [])
    {
        $this->user = $user;
        $this->bank = config('app.bank') ?? [];
        $this->exam = $exam;
        $this->amount = $amount;
        $this->selectedModules = $selectedModules;
    }

    public function build(): self
    {
        return $this->subject('Informations de paiement')
            ->view('emails.payment-instructions');
    }
}
