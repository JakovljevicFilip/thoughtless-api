<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Mail\PasswordChangedMail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Facades\Mail;

final class SendPasswordChangedEmail implements ShouldQueue, ShouldQueueAfterCommit
{
    public function handle(PasswordReset $event): void
    {
        Mail::to($event->user->email)->send(new PasswordChangedMail($event->user));
    }
}

