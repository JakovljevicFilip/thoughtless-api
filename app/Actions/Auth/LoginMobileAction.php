<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class LoginMobileAction
{
    /**
     * @return array{access_token: string, token_type: string, expires_in: int}
     *
     * @throws RuntimeException
     */
    public function execute(string $email, string $password, string $deviceName = 'mobile'): array
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new RuntimeException('Invalid credentials.', 401);
        }

        if (! $user->hasVerifiedEmail()) {
            throw new RuntimeException('Please verify your email before continuing.', 403);
        }

        $user->tokens()->where('name', $deviceName)->delete();

        $plain = $user->createToken($deviceName, ['*'])->plainTextToken;

        $minutes = config('sanctum.expiration');
        $expiresIn = $minutes ? $minutes * 60 : 0;

        return [
            'access_token' => $plain,
            'token_type'   => 'Bearer',
            'expires_in'   => $expiresIn,
        ];
    }
}
