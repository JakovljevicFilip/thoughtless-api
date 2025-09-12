<?php
declare(strict_types=1);

namespace App\Contracts\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

interface ScheduleAccountRemovalContract
{
    public function execute(User $user, string $plainPassword): void;
}
