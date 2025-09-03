<?php
declare(strict_types=1);

namespace App\Exceptions\Auth;

use RuntimeException;

final class EmailNotVerified extends \RuntimeException
{
    public function __construct() {
        parent::__construct('Please verify your email before continuing.', 403);
    }
}
