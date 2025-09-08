<?php
declare(strict_types=1);

namespace App\Tokens\EmailVerification;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

final class EmailVerificationService
{
    /**
     * @return bool true if user was just verified, false if already verified
     */
    public function markVerifiedAndLogin(User $user): bool
    {
        $justVerified = false;

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            $justVerified = true;
        }

        Auth::login($user);

        return $justVerified;
    }
}
