<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

use App\Models\User;

interface LoginWebContract
{
    /**
     * Cookie (session) login on the "web" guard.
     * @throws \App\Exceptions\Auth\InvalidCredentials
     * @throws \App\Exceptions\Auth\EmailNotVerified
     */
    public function execute(string $email, string $password, bool $remember = false): User;
}
