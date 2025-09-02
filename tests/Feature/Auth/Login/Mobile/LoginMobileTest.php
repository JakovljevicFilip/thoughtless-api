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
}
