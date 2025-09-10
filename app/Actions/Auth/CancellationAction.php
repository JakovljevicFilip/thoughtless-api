<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\CancellationActionContract;
use App\Exceptions\Auth\InvalidCancellationToken;
use App\Exceptions\Auth\NoDeletionScheduled;
use App\Exceptions\Auth\UserNotFound;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class CancellationAction implements CancellationActionContract
{
    public function execute(string $userId, string $plainToken): void
    {
        /** @var User|null $user */
        $user = User::find($userId);
        if (! $user) {
            throw new UserNotFound();
        }
        if (is_null($user->marked_for_deletion_at)) {
            throw new NoDeletionScheduled();
        }

        $row = DB::table('deletion_cancellation_tokens')
            ->where('user_id', $userId)
            ->first();

        if (! $row || ! password_verify($plainToken, (string) $row->token_hash)) {
            throw new InvalidCancellationToken();
        }
    }
}
