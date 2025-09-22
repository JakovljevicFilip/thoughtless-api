<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

final class CancelRemovalOnLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->marked_for_deletion_at) {
            $user->forceFill(['marked_for_deletion_at' => null])->save();

            DB::table('deletion_cancellation_tokens')
                ->where('user_id', $user->id)
                ->delete();
        }
    }
}
