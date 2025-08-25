<?php
declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

final class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rule = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        $validator = validator([$attribute => $value], [$attribute => $rule]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $fail($message);
            }
        }
    }
}
