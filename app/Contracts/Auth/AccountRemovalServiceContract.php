<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

use App\Models\User;

interface AccountRemovalServiceContract
{
    /** Returns the plain cancellation token (useful for tests). */
    public function remove(User $user): string;

    /** Cancels the scheduled deletion and clears tokens. */
    public function cancel(User $user, string $plainToken): void;
}
