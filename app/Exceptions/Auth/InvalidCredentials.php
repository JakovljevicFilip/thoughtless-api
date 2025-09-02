<?php
declare(strict_types=1);

namespace App\Exceptions\Auth;

use RuntimeException;

final class InvalidCredentials extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid credentials.', 401);
    }
}
