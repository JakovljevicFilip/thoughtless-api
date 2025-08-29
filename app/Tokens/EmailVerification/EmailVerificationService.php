<?php
declare(strict_types=1);

namespace App\Tokens\EmailVerification;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

final class EmailVerificationService
{
    public function markVerifiedAndLogin(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Auth::login($user);
    }
}
