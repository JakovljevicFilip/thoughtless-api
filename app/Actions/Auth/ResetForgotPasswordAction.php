<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\ResetForgotPasswordContract;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RuntimeException;

final class ResetForgotPasswordAction implements ResetForgotPasswordContract
{
    public function execute(string $email, string $password, string $passwordConfirmation, string $token): array
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'token' => $token,
        ];

        $status = Password::reset(
            $credentials,
            function ($user, $newPassword) {
                $user->forceFill([
                    'password' => Hash::make($newPassword),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return match ($status) {
            Password::PASSWORD_RESET => ['message' => 'Password has been reset.'],
            Password::INVALID_USER   => throw new RuntimeException('The selected email is invalid.', 422),
            Password::INVALID_TOKEN  => throw new RuntimeException('This password reset token is invalid.', 400),
            default                  => throw new RuntimeException(__($status), 400),
        };
    }
}
