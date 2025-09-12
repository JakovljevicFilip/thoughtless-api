<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

//TODO: If user logs in while their account is account is marked for removal, the removal is cancelled.
final class LoginMobileTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_returns_token_and_user_and_me_endpoint_works(): void
    {
        $user = User::factory()->create([
            'first_name'        => 'John',
            'last_name'         => 'Doe',
            'email'             => 'john.doe@gmail.com',
            'password'          => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $res = $this->postJson('/api/auth/mobile/login', [
            'email'       => 'john.doe@gmail.com',
            'password'    => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => ['id', 'first_name', 'last_name', 'email'],
            ])
            ->assertJsonFragment([
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john.doe@gmail.com',
            ]);

        $token = $res->json('access_token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'message' => 'User is logged in.',
                'user' => [
                    'id'         => (string) $user->id,
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'email'      => 'john.doe@gmail.com',
                ],
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function logging_in_twice_with_same_device_replaces_previous_token(): void
    {
        // stateless Sanctum for API tokens
        config()->set('sanctum.stateful', []);

        $user = User::factory()->create([
            'email'             => 'john.doe@gmail.com',
            'password'          => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $r1 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john.doe@gmail.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk();
        $token1 = $r1->json('access_token');
        [$id1] = explode('|', $token1, 2);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $r2 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john.doe@gmail.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk();
        $token2 = $r2->json('access_token');
        [$id2] = explode('|', $token2, 2);

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNotSame($id1, $id2);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => (int) $id1]);

        $this->withHeader('Authorization', "Bearer {$token1}")
            ->getJson('/api/me')
            ->assertStatus(401);

        $this->withHeader('Authorization', "Bearer {$token2}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'message' => 'User is logged in.',
                'user' => ['id' => (string) $user->id],
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function logging_in_with_different_devices_keeps_both_tokens(): void
    {
        config()->set('sanctum.stateful', []);

        $user = User::factory()->create([
            'email'             => 'john@gmail.com',
            'password'          => Hash::make('Correct#123'),
            'email_verified_at' => now(),
        ]);

        $r1 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@gmail.com',
            'password' => 'Correct#123',
            'device_name' => 'Pixel 7',
        ])->assertOk();
        $t1 = $r1->json('access_token');

        $r2 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@gmail.com',
            'password' => 'Correct#123',
            'device_name' => 'iPad',
        ])->assertOk();
        $t2 = $r2->json('access_token');

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->withHeader('Authorization', "Bearer {$t1}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'message' => 'User is logged in.',
                'user' => ['id' => (string) $user->id],
            ]);

        $this->withHeader('Authorization', "Bearer {$t2}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'message' => 'User is logged in.',
                'user' => ['id' => (string) $user->id],
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tokens_do_not_expire_and_expires_in_is_null(): void
    {
        config()->set('sanctum.expiration', null);

        $user = User::factory()->create([
            'email'             => 'forever@gmail.com',
            'password'          => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $res = $this->postJson('/api/auth/mobile/login', [
            'email' => 'forever@gmail.com',
            'password' => 'StrongPass1!',
            'device_name' => 'My Phone',
        ])->assertOk()
            ->assertJson(['expires_in' => null]);

        $token = $res->json('access_token');
        $this->travel(5)->years();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'message' => 'User is logged in.',
                'user' => ['id' => (string) $user->id],
            ]);
    }
}
