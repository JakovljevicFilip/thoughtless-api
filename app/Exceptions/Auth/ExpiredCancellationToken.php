<?php
declare(strict_types=1);

namespace App\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class ExpiredCancellationToken extends HttpException
{
    public function __construct()
    {
        parent::__construct(422, 'This cancellation link has expired.');
    }
}
