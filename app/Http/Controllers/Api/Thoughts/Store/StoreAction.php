<?php

namespace App\Http\Controllers\Api\Thoughts\Store;

use App\Models\Thought;
use App\Models\User;
use Illuminate\Support\Collection;

class StoreAction
{
    /**
     * @return Collection<int, Thought>
     */
    public static function execute(User $user, array $thoughts): Collection
    {
        return collect($thoughts)->map(
            fn (array $thought) => $user->thoughts()->create([
                'content'    => $thought['content'],
                'created_at' => $thought['created_at'],
            ])
        );
    }
}
