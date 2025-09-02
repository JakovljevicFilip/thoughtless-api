<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class LoginWebAction
{
    /**
     * Attempt a cookie-based (session) login on the web guard.
     *
     * @throws RuntimeException (401 invalid creds, 403 unverified)
     */
    public function execute(string $email, string $password, bool $remember = false): User
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, (string) $user->password)) {
            throw new RuntimeException('Invalid credentials.', 401);
        }

        if (! $user->hasVerifiedEmail()) {
            throw new RuntimeException('Please verify your email before continuing.', 403);
        }

        Auth::guard('web')->login($user, $remember);

        return $user;
    }
}
