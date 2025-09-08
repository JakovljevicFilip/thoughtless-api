<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build(): self
    {
        return $this->subject('Your password was changed')
            ->view('emails.auth.password.changed.changed')
            ->text('emails.auth.password.changed.changed_plain')
            ->with([
                'suite' => config('app.suite_name'),
                'supportUrl' => url('/forgot-password'),
                'logo' => asset('icons/favicon-512x512.png'),
            ]);
    }
}
