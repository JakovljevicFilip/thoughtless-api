<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\Thought;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

final class ThoughtSeeder extends Seeder
{
    public function run(): void
    {
        Thought::unguard();

        $user = User::where('email', 'test@example.com')->firstOrFail();

        Thought::create([
            'id'         => '128cb220-b75e-4080-9f1b-000000000001',
            'user_id'    => $user->id,
            'content'    => 'Thought',
            'created_at' => CarbonImmutable::parse('2025-08-11 07:18:20'),
            'updated_at' => CarbonImmutable::parse('2025-08-11 07:18:20'),
        ]);

        Thought::create([
            'id'         => '52033e3f-8566-453d-9f1b-000000000002',
            'user_id'    => $user->id,
            'content'    => 'New Thought 123',
            'created_at' => CarbonImmutable::parse('2025-08-11 07:18:27'),
            'updated_at' => CarbonImmutable::parse('2025-08-11 07:18:27'),
        ]);

        Thought::create([
            'id'         => 'f7f0ef68-4be4-4967-9f1b-000000000003',
            'user_id'    => $user->id,
            'content'    => "It's working MOFOs!",
            'created_at' => CarbonImmutable::parse('2025-08-11 07:18:33'),
            'updated_at' => CarbonImmutable::parse('2025-08-11 07:18:33'),
        ]);
    }
}
