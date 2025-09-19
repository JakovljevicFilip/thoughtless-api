<?php
declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class MustBeUnverified implements ValidationRule
{
    public function __construct(private User $user) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->user->hasVerifiedEmail()) {
            $fail('Email already verified.');
        }
    }
}
