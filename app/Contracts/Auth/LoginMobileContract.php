<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

interface LoginMobileContract
{
    /**
     * @return array{
     *   access_token: string,
     *   token_type: string,
     *   expires_in: int|null,
     *   user: array{id: string, first_name: string, last_name: string, email: string}
     * }
     */
    public function execute(string $email, string $password, string $deviceName = 'mobile'): array;
}
