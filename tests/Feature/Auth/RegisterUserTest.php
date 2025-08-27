<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Mail\ConfirmationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_requires_all_mandatory_fields(): void
    {
        $this->postJson('/api/user/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'password',
                'password_confirmation',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_fields_exceed_max_length(): void
    {
        $payload = [
            'first_name' => str_repeat('a', User::MAX_FIRST_NAME_LENGTH + 1),
            'last_name'  => str_repeat('b', User::MAX_LAST_NAME_LENGTH + 1),
            'email'      => str_repeat('c', User::MAX_EMAIL_LENGTH + 1) . '@test.com',
            'password'   => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_email_is_invalid(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'not-an-email',
            'password'   => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_passwords_do_not_match(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'password'   => 'password123',
            'password_confirmation' => 'different123',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password_confirmation']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_email_is_not_unique(): void
    {
        $existingEmail = 'john@example.com';

        User::factory()->create([
            'email' => $existingEmail,
        ]);

        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => $existingEmail,
            'password'   => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_weak_passwords(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john.weak@example.com',
            'password'   => 'aaa123', // too short, no symbol, no uppercase
            'password_confirmation' => 'aaa123',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_user_can_register(): void
    {
        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john.doe@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $response = $this->postJson('/api/user/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function a_confirmation_email_is_sent_after_registration(): void
    {
        Mail::fake();

        $payload = [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane.doe@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        Mail::assertSent(ConfirmationMail::class, function ($mail) use ($payload) {
            return $mail->hasTo($payload['email']);
        });
    }
}
