<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginMobileTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_and_receive_opaque_token(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $res = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);

        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Optional: prove the token works against an auth:sanctum route
        $token = $res->json('access_token');
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['id' => (string) $user->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Correct#123'),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/api/auth/mobile/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong',
            'device_name' => 'Pixel 7',
        ])
            ->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unverified_users_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'alice@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => null,
        ]);

        $this->postJson('/api/auth/mobile/login', [
            'email' => 'alice@example.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])
            ->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email before continuing.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_does_not_expire(): void
    {
        // Ensure tokens never expire
        config()->set('sanctum.expiration', null);

        $user = User::factory()->create([
            'email' => 'forever@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        // Login to get opaque token
        $res = $this->postJson('/api/auth/mobile/login', [
            'email' => 'forever@example.com',
            'password' => 'StrongPass1!',
            'device_name' => 'My Phone',
        ])->assertOk();

        $token = $res->json('access_token');
        $this->assertNotEmpty($token);

        // Jump far into the future — token should still work
        $this->travel(5)->years();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['id' => (string) $user->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function logging_in_twice_with_same_device_replaces_previous_token(): void
    {
        config()->set('sanctum.stateful', []);

        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        // First login
        $res1 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk();

        $token1 = $res1->json('access_token');
        [$id1] = explode('|', $token1, 2);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Second login with SAME device name
        $res2 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
            'device_name' => 'Pixel 7',
        ])->assertOk();

        $token2 = $res2->json('access_token');
        [$id2] = explode('|', $token2, 2);

        // Only one token should exist (the new one)
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNotSame($id1, $id2);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => (int) $id1]);

        // Old token should no longer authenticate
        $this->withHeader('Authorization', "Bearer {$token1}")
            ->getJson('/api/me')
            ->assertStatus(401);

        // New token should authenticate fine
        $this->withHeader('Authorization', "Bearer {$token2}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['id' => (string) $user->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function logging_in_with_different_device_keeps_both_tokens(): void
    {
        config()->set('sanctum.stateful', []);

        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Correct#123'),
            'email_verified_at' => now(),
        ]);

        // First device
        $res1 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'jane@example.com',
            'password' => 'Correct#123',
            'device_name' => 'Pixel 7',
        ])->assertOk();
        $token1 = $res1->json('access_token');

        // Second device
        $res2 = $this->postJson('/api/auth/mobile/login', [
            'email' => 'jane@example.com',
            'password' => 'Correct#123',
            'device_name' => 'iPad',
        ])->assertOk();
        $token2 = $res2->json('access_token');

        // Two tokens should exist
        $this->assertDatabaseCount('personal_access_tokens', 2);

        // Both tokens should authenticate
        $this->withHeader('Authorization', "Bearer {$token1}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['id' => (string) $user->id]);

        $this->withHeader('Authorization', "Bearer {$token2}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['id' => (string) $user->id]);
    }
}
