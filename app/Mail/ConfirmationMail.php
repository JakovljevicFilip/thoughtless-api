<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class ConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject('Welcome to ' . config('app.suite_name'))
            ->view('emails.auth.confirmation.confirmation')
            ->text('emails.auth.confirmation.confirmation_plain');
    }
}
