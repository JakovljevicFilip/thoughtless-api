<?php
declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\Auth\AccountRemovalServiceContract;
use App\Exceptions\Auth\ExpiredCancellationToken;
use App\Exceptions\Auth\InvalidCancellationToken;
use App\Exceptions\Auth\NoDeletionScheduled;
use App\Jobs\PruneDeletedUserJob;
use App\Mail\AccountRemovalScheduledMail;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final readonly class AccountRemovalService implements AccountRemovalServiceContract
{
    public function __construct(private ConnectionInterface $db) {}

    public function remove(User $user): string
    {
        $grace = (int) Config::get('auth.deletion_grace_hours', 24);
        $plain = Str::random(64);

        $this->db->transaction(function () use ($user, $grace, $plain) {
            // Mark for deletion
            $user->forceFill(['marked_for_deletion_at' => now()])->save();

            // Revoke sessions
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
            // Revoke access tokens
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', $user::class)
                    ->where('tokenable_id', $user->id)
                    ->delete();
            }

            // Store hashed cancellation token
            DB::table('deletion_cancellation_tokens')->insert([
                'id'         => (string) Str::uuid(),
                'user_id'    => $user->id,
                'token_hash' => password_hash($plain, PASSWORD_BCRYPT),
                'expires_at' => now()->addHours($grace),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Email cancel link
            $cancelUrl = rtrim((string) config('app.frontend_url'), '/')
                . '/cancel-removal?id=' . urlencode((string) $user->id)
                . '&token=' . urlencode($plain);

            Mail::to($user->email)->send(new AccountRemovalScheduledMail($user, $grace, $cancelUrl));

            PruneDeletedUserJob::dispatch($user->id)->delay(now()->addHours($grace));
        });

        return $plain;
    }

    public function cancel(User $user, string $plainToken): void
    {
        $this->verify($user, $plainToken);
        $this->db->transaction(function () use ($user) {
            $user->forceFill(['marked_for_deletion_at' => null])->save();
            DB::table('deletion_cancellation_tokens')->where('user_id', $user->id)->delete();
        });
    }

    private function verify(User $user, string $plainToken): void
    {
        if (! $user || is_null($user->marked_for_deletion_at)) {
            throw new NoDeletionScheduled();
        }

        $row = DB::table('deletion_cancellation_tokens')
            ->where('user_id', $user->id)
            ->first();

        if (! $row || ! password_verify($plainToken, (string) $row->token_hash)) {
            throw new InvalidCancellationToken();
        }

        if (now()->greaterThan($row->expires_at)) {
            throw new ExpiredCancellationToken();
        }
    }
}
