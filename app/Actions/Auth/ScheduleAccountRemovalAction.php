<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\ScheduleAccountRemovalContract;
use App\Jobs\PruneDeletedUserJob;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

final readonly class ScheduleAccountRemovalAction implements ScheduleAccountRemovalContract
{
    public function __construct(private ConnectionInterface $db) {}

    public function execute(User $user, string $plainPassword): void
    {
        if (! Hash::check($plainPassword, (string) $user->password)) {
            abort(422, 'The provided password is incorrect.');
        }

        $this->db->transaction(function () use ($user) {
            $user->forceFill(['marked_for_deletion_at' => now()])->save();

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }

            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', $user::class)
                    ->where('tokenable_id', $user->id)
                    ->delete();
            }

            $grace = (int) Config::get('auth.deletion_grace_hours', 24);
            PruneDeletedUserJob::dispatch($user->id)->delay(now()->addHours($grace));
        });
    }
}
