<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

interface VerifyForgotPasswordContract
{
    /**
     * Verifies that the password reset token for the given email is valid and not expired.
     * Returns an array you can extend later if you want to pass metadata.
     *
     * @return array{message: string}
     */
    public function execute(string $email, string $plainToken): array;
}
