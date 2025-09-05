<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\VerifyForgotPasswordContract;
use App\Exceptions\Auth\InvalidResetLink;
use App\Exceptions\Auth\ExpiredResetLink;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class VerifyForgotForgotPasswordAction implements VerifyForgotPasswordContract
{
    public function execute(string $email, string $plainToken): array
    {
        /** @var User|null $user */
        $user = User::where('email', $email)->first();

        // If user doesn't exist, we treat it as a validation problem (like your Request does)
        if (! $user) {
            // keep this aligned with your FormRequest rule `exists:users,email`
            throw new InvalidResetLink('The selected email is invalid.', 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (! $record || ! Hash::check($plainToken, $record->token)) {
            throw new InvalidResetLink('This password reset link is invalid.', 422);
        }

        $minutes  = (int) (config('auth.passwords.users.expire') ?? 60);
        $issuedAt = CarbonImmutable::parse($record->created_at);

        if ($issuedAt->lt(now()->subMinutes($minutes))) {
            throw new ExpiredResetLink('This password reset link has expired.', 422);
        }

        return ['message' => 'Valid password reset link.'];
    }
}
