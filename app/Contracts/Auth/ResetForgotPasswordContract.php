<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

interface ResetForgotPasswordContract
{
    /**
     * Resets the user’s password if the token is valid.
     *
     * @return array{message: string}
     */
    public function execute(string $email, string $password, string $passwordConfirmation, string $token): array;
}
