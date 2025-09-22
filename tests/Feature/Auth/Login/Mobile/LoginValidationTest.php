<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

final class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_email_password_and_device_name(): void
    {
        $response = $this->postJson('/api/auth/mobile/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password', 'device_name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function email_must_be_a_valid_email(): void
    {
        $this->postJson('/api/auth/mobile/login', [
            'email'       => 'not-an-email',
            'password'    => 'anything',
            'device_name' => 'Some Device',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function device_name_has_to_be_string(): void
    {
        $this->postJson('/api/auth/mobile/login', [
            'email'       => 'john@example.com',
            'password'    => 'StrongPass1!',
            'device_name' => ['phone'],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['device_name']);
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
        $this->postJson('/api/auth/mobile/login', [
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
