<?php

namespace App\Mail;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultsPublished extends Mailable
{
    use Queueable, SerializesModels;

    public Exam $exam;
    public User $user;

    public function __construct(Exam $exam, User $user)
    {
        $this->exam = $exam;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->subject("Résultats publiés - {$this->exam->name}")
            ->view('emails.results-published');
    }
}
