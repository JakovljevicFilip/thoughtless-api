<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

final class ForgotPasswordMail extends Mailable implements ShouldQueue
{
    public function __construct(
        public User $user,
        public string $resetUrl
    ) {
        $firstName = $this->user->first_name ?? $this->user->name ?? '';

        $this->subject = "Hey {$firstName}, did you forget your password?";
        $this->view = 'emails.auth.passwords.reset';
        $this->textView = 'emails.auth.passwords.reset_plain';

        $this->viewData = [
            'suite'     => config('app.suite_name'),
            'firstName' => $firstName,
            'resetUrl'  => $this->resetUrl,
            'logo'      => asset('icons/favicon-512x512.png'),
        ];
    }

    public function build(): self
    {
        return $this;
    }
}
