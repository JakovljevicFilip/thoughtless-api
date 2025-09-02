<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\LoginWebContract;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class LoginWebAction implements LoginWebContract
{
    public function execute(string $email, string $password, bool $remember = false): User
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, (string) $user->password)) {
            throw new InvalidCredentials();
        }

        if (! $user->hasVerifiedEmail()) {
            throw new EmailNotVerified();
        }

        Auth::guard('web')->login($user, $remember);

        return $user;
    }
}
