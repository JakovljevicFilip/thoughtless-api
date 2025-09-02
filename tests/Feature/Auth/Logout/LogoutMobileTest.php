<?php
declare(strict_types=1);

namespace Feature\Auth\Logout;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LogoutMobileTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_logout_mobile(): void
    {
        config()->set('sanctum.stateful', []);

        $this->postJson('/api/auth/mobile/logout')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_logout_and_current_token_is_revoked(): void
    {
        config()->set('sanctum.stateful', []);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('iPhone', ['*'])->plainTextToken;
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/mobile/logout')
            ->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
