<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\AccountRemovalServiceContract;
use App\Contracts\Auth\CancellationActionContract;
use App\Exceptions\Auth\UserNotFound;
use App\Models\User;

final readonly class CancellationAction implements CancellationActionContract
{
    public function __construct(
        private AccountRemovalServiceContract $accountRemovalService,
    ) {}

    public function execute(string $userId, string $plainToken): void
    {
        /** @var User|null $user */
        $user = User::find($userId);
        if (! $user) {
            throw new UserNotFound();
        }
        $this->accountRemovalService->cancel($user, $plainToken);
    }
}
