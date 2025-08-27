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

    public function __construct(
        public User $user
    ) {}

    public function build(): self
    {
        return $this->subject('Confirm your account')
            ->markdown('emails.auth.confirmation', [
                'suiteName' => config('app.suite_name'),
            ]);
    }
}
