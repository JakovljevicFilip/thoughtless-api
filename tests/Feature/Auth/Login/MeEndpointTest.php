<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class MeEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function me_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/api/me')
            ->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function me_returns_user_when_authenticated_via_web_session(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane@example.com',
            'email_verified_at' => now(),
        ]);

        // Simulate logged-in session
        $this->actingAs($user, 'web');

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'id'         => (string) $user->id,
                'first_name' => 'Jane',
                'last_name'  => 'Doe',
                'email'      => 'jane@example.com',
            ])
            // optional: ensure there are no unexpected keys
            ->assertJsonCount(4);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function me_returns_user_when_authenticated_via_sanctum_token(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name'  => 'Smith',
            'email'      => 'john@example.com',
            'email_verified_at' => now(),
        ]);

        // Simulate Bearer token auth
        Sanctum::actingAs($user, abilities: ['*']);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJson([
                'id'         => (string) $user->id,
                'first_name' => 'John',
                'last_name'  => 'Smith',
                'email'      => 'john@example.com',
            ])
            ->assertJsonCount(4);
    }
}
