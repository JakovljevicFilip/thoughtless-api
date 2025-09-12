<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class PruneDeletedUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int|string $userId) {}

    public function handle(ConnectionInterface $db): void
    {
        /** @var User|null $user */
        $user = User::query()->find($this->userId);
        if (! $user) {
            return;
        }

        if (is_null($user->marked_for_deletion_at)) {
            return;
        }

        $db->transaction(function () use ($user) {
            if (Schema::hasTable('thoughts')) {
                DB::table('thoughts')->where('user_id', $user->id)->delete();
            }

            $user->delete();
        });
    }
}
