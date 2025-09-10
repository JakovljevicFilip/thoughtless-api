<?php
declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

final class AccountRemovalScheduledMail extends Mailable
{
    public function __construct(public User $user, public int $graceHours, public string $cancelUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your account will be deleted in {$this->graceHours} hours"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.removal.removal',
            text: 'emails.auth.removal.removal_plain',
            with: [
                'suite'     => config('app.suite_name'),
                'logo'      => asset('icons/favicon-512x512.png'),
                'hours'     => $this->graceHours,
                'cancelUrl' => $this->cancelUrl,
            ]
        );
    }
}

