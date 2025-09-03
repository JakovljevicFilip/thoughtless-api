<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Registration;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationUserTest extends TestCase
{
    use RefreshDatabase;

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

        $this->postJson('/api/user/register', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email'      => $payload['email'],
            'first_name' => $payload['first_name'],
            'last_name'  => $payload['last_name'],
        ]);
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

        $this->postJson('/api/user/register', $payload)->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'email_verified_at' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_email_contains_expected_link_format(): void
    {
        Notification::fake();

        $payload = [
            'first_name' => 'Alice',
            'last_name'  => 'Verify',
            'email'      => 'alice.verify@example.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)->assertStatus(201);

        $user = User::where('email', $payload['email'])->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification, array $channels) use ($user) {
            $mail = $notification->toMail($user);
            $rendered = (string) $mail->render();

            $feUrl   = preg_quote(rtrim(config('app.frontend_url'), '/'), '/');
            $pattern = "/{$feUrl}\/verify\/{$user->id}\/[A-Za-z0-9=_\-]+/";

            $this->assertMatchesRegularExpression($pattern, $rendered);

            return true;
        });
    }
}
