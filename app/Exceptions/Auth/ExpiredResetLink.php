<?php
declare(strict_types=1);

namespace App\Exceptions\Auth;

use RuntimeException;

final class ExpiredResetLink extends RuntimeException {}
