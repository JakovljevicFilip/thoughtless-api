<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class AccountRemovalScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public int $graceHours)
    {
        $this->subject("Your account will be deleted in {$graceHours} hours")
            ->view('emails.auth.removal.removal')
            ->text('emails.auth.removal.removal_plain')
            ->with([
                'suite' => config('app.suite_name'),
//                TODO: I don't need to pass logo every time.
                'logo' => asset('icons/favicon-512x512.png'),
                'cancelUrl' => url('/login'),
                'hours' => $graceHours,
            ]);
    }
}
