<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {}

    public function build(): self
    {
        $frontend = rtrim(config('app.frontend_url'), '/');

        $verifyUrl = "{$frontend}/verify/"
            . urlencode($this->user->email)
            . "/" . urlencode($this->token);

        return $this->subject('Welcome to ' . config('app.suite_name'))
            ->view('emails.auth.confirmation.confirmation')
            ->text('emails.auth.confirmation.confirmation_plain')
            ->with([
                'suite'     => config('app.suite_name'),
                'verifyUrl' => $verifyUrl,
                'logo' => asset('icons/favicon-512x512.png'),
            ]);
    }
}
