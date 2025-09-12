<?php
declare(strict_types=1);

namespace App\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class NoDeletionScheduled extends HttpException
{
    public function __construct()
    {
        parent::__construct(409, 'No deletion is scheduled.');
    }
}
