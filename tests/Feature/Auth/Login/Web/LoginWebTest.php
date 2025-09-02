<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginWebTest extends TestCase
{
    use RefreshDatabase;

    private function csrf(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertNoContent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_and_get_session_cookie(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $this->csrf();

        $this->postJson('/api/auth/web/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
        ])->assertOk()
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'first_name', 'last_name', 'email'],
            ]);

        $this->assertAuthenticatedAs($user, 'web');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unverified_users_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'alice@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => null,
        ]);

        $this->csrf();

        $this->postJson('/api/auth/web/login', [
            'email' => 'alice@example.com',
            'password' => 'StrongPass1!',
        ])
            ->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email before continuing.']);

        $this->assertGuest('web');
    }
}
