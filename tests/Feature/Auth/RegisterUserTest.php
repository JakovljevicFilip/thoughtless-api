<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Mail\ConfirmationMail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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
    public function a_verification_notification_is_sent_after_registration(): void
    {
        Notification::fake();

        $payload = [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane.doe@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        $user = User::where('email', $payload['email'])->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function email_is_not_verified_after_registration(): void
    {
        $payload = [
            'first_name' => 'Eve',
            'last_name'  => 'Tester',
            'email'      => 'eve.tester@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'email_verified_at' => null, // ✅ ensures DB column is NULL
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function confirmation_email_includes_plain_text_fallback(): void
    {
        $user = User::factory()->make([
            'first_name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $mailable = new ConfirmationMail($user);

        // Render HTML
        $html = $mailable->render();
        $this->assertStringContainsString('Confirm Your Email', $html);

        // Render plain text
        $text = view($mailable->textView, $mailable->buildViewData())->render();
        $this->assertStringContainsString('Confirm Your Email', $text);

        // Both should contain the link
        $verifyUrl = url('/verify?email=' . urlencode($user->email));
        $this->assertStringContainsString($verifyUrl, $html);
        $this->assertStringContainsString($verifyUrl, $text);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_provides_valid_verification_url(): void
    {
        Notification::fake();

        $payload = [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane.valid@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        $user = User::where('email', $payload['email'])->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            $mail = (string) $notification->toMail($user)->render();

            // FE link should be present
            $this->assertStringContainsString(config('app.frontend_url') . '/verify/', $mail);

            // Extract the token from the URL and decode it
            preg_match('#/verify/([A-Za-z0-9=+/]+)#', $mail, $matches);
            $this->assertNotEmpty($matches, 'No verification token found in email');

            $token = base64_decode($matches[1], true);
            $this->assertNotFalse($token, 'Invalid base64 token in email');

            $data = json_decode($token, true);
            $this->assertIsArray($data);

            // Check the expected structure
            $this->assertSame($user->getKey(), $data['id']);
            $this->assertSame(sha1($user->getEmailForVerification()), $data['hash']);
            $this->assertArrayHasKey('sig', $data);
            $this->assertArrayHasKey('exp', $data);

            return true;
        });
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_email_points_to_frontend_url(): void
    {
        Notification::fake();

        $payload = [
            'first_name' => 'Frank',
            'last_name'  => 'Frontend',
            'email'      => 'frank.fe@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        $user = User::where('email', $payload['email'])->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            $mail = (string) $notification->toMail($user)->render();

            $this->assertStringContainsString(config('app.frontend_url') . '/verify/', $mail);

            return true;
        });
    }
}
