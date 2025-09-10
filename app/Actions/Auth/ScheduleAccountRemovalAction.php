<?php
declare(strict_types=1);

namespace App\Actions\Auth;

use App\Contracts\Auth\ScheduleAccountRemovalContract;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Hash;

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
        });
    }
}
