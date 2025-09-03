<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\LoginMobileContract;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class LoginMobileAction implements LoginMobileContract
{
    /**
     * @return array{
     *   access_token: string,
     *   token_type: string,
     *   expires_in: int|null,
     *   user: array{id: string, first_name: string, last_name: string, email: string}
     * }
     */
    public function execute(string $email, string $password, string $deviceName = 'mobile'): array
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidCredentials();
        }
        if (! $user->hasVerifiedEmail()) {
            throw new EmailNotVerified();
        }

        $user->tokens()->where('name', $deviceName)->delete();

        $plain = $user->createToken($deviceName, ['*'])->plainTextToken;

        return [
            'access_token' => $plain,
            'token_type'   => 'Bearer',
            'expires_in'   => null,
            'user' => [
                'id'         => (string) $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
            ],
        ];
    }
}
