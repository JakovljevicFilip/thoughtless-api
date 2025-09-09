<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::unguard();

        User::firstOrCreate(
            ['id' => '11111111-1111-1111-1111-111111111111'],
            [
                'first_name' => 'Test',
                'last_name'  => 'User',
                'email'      => 'test@example.com',
                'password'   => Hash::make('password'),
            ]
        );
    }
}
