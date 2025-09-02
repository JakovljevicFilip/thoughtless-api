<?php
declare(strict_types=1);

namespace Feature\Auth\Logout;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LogoutWebTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_logout(): void
    {
        // no session / no authenticated user
        $this->postJson('/api/auth/web/logout')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);

        $this->assertGuest('web');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_logout_and_session_is_destroyed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->be($user, 'web');

        $this->postJson('/api/auth/web/logout')
            ->assertNoContent();

        $this->assertGuest('web');
    }
}
