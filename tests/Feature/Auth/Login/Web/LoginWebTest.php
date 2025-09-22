<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

//TODO: If user logs in while their account is account is marked for removal, the removal is cancelled.
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
            'email' => 'john@gmail.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $this->csrf();

        $this->postJson('/api/auth/web/login', [
            'email' => 'john@gmail.com',
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
            'email' => 'john@gmail.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => null,
        ]);

        $this->csrf();

        $this->postJson('/api/auth/web/login', [
            'email' => 'john@gmail.com',
            'password' => 'StrongPass1!',
        ])
            ->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email before continuing.']);

        $this->assertGuest('web');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_login_populates_sessions_user_id(): void
    {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $this->csrf();
        $this->postJson('/api/auth/web/login', [
            'email' => 'john@gmail.com',
            'password' => 'StrongPass1!',
        ])->assertOk();
        $this->getJson('/api/me')->assertOk();

        $this->assertDatabaseHas('sessions', [
            'user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_cancels_account_removal(): void
    {
        $user = User::factory()->create([
            'email' => 'john@gmail.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
            'marked_for_deletion_at' => now(),
        ]);

        $plain = Str::random(64);
        DB::table('deletion_cancellation_tokens')->insert([
            'id'         => (string) Str::uuid(),
            'user_id'    => $user->id,
            'token_hash' => password_hash($plain, PASSWORD_BCRYPT),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('deletion_cancellation_tokens', [
            'user_id' => $user->id,
        ]);

        $this->csrf();
        $this->postJson('/api/auth/web/login', [
            'email' => 'john@gmail.com',
            'password' => 'StrongPass1!',
        ])->assertOk();

        $user->refresh();
        $this->assertNull($user->marked_for_deletion_at);

        $this->assertDatabaseMissing('deletion_cancellation_tokens', [
            'user_id' => $user->id,
        ]);
    }
}
