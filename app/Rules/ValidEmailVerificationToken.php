<?php
declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use App\Tokens\EmailVerification\EmailVerificationToken;
use App\Tokens\EmailVerification\EmailVerificationTokenValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class ValidEmailVerificationToken implements ValidationRule
{
    public function __construct(private User $user) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $parsed = EmailVerificationToken::parse((string) $value);
        } catch (\InvalidArgumentException) {
            $fail('Invalid verification token.');
            return;
        }

        if ($parsed->isExpired()) {
            $fail('The verification link has expired.');
            return;
        }

        $svc = app(EmailVerificationTokenValidator::class);

        if (! $svc->emailHashMatches($this->user, $parsed)) {
            $fail('Invalid verification hash.');
        }

        if (! $svc->signatureValid((string) $this->user->email, $parsed)) {
            $fail('Invalid verification signature.');
        }
    }
}
