<?php

namespace App\Console\Commands;

use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

final class TestConfirmationMail extends Command
{
    protected $signature = 'mail:test-confirmation';
    protected $description = 'Send confirmation mail to the first user';

    public function handle(): void
    {
        $user = User::firstOrFail();
        Mail::to($user->email)->send(new VerificationMail($user));
        $this->info("Sent to {$user->email}");
    }
}
