<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

interface CancellationActionContract
{
    /** @throws \RuntimeException on invalid/expired token or when nothing to cancel */
    public function execute(string $userId, string $plainToken): void;
}
