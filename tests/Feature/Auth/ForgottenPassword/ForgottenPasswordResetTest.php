<?php

namespace Tests\Feature\Auth\ForgottenPassword;

use App\Models\User;
// use App\Notifications\PasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgottenPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_email_and_token_when_missing()
    {
        $response = $this->postJson('/api/user/forgot-password/reset', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_a_valid_email_format()
    {
        $response = $this->postJson('/api/user/forgot-password/reset', [
            'email' => 'not-an-email',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'token' => 'some-token',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function passwords_must_match()
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);
        $token = Password::createToken($user);

        $response = $this->postJson('/api/user/forgot-password/reset', [
            'email' => $user->email,
            'password' => 'secret123',
            'password_confirmation' => 'different123',
            'token' => $token,
        ]);

        // Validation should fail before hitting the Action
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_with_invalid_token()
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->postJson('/api/user/forgot-password/reset', [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'token' => 'invalid-token',
        ]);

        // Wrong token should go through Action and return 400
        $response->assertStatus(400)
            ->assertJson(['message' => 'This password reset token is invalid.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_resets_the_password_with_a_valid_token_and_expires_the_token()
    {
        Event::fake();

        $user = User::factory()->create(['email' => 'jane@example.com']);
        $token = Password::createToken($user);

        $this->postJson('/api/user/forgot-password/reset', [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'token' => $token,
        ])->assertStatus(200);

        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password), 'Password was not updated');
        Event::assertDispatched(PasswordReset::class);

        // Token should now be invalid (single-use)
        $this->postJson('/api/user/forgot-password/reset', [
            'email' => $user->email,
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
            'token' => $token,
        ])->assertStatus(400)
            ->assertJson(['message' => 'This password reset token is invalid.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_notification_when_password_is_reset()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'jane@example.com']);
        $token = Password::createToken($user);

        $this->postJson('/api/user/forgot-password/reset', [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
            'token' => $token,
        ])->assertStatus(200);

        // TODO: Add a a notification your password has been changed.
        // Uncomment once PasswordChangedNotification + listener are in place
        // Notification::assertSentTo(
        //     $user,
        //     PasswordChangedNotification::class
        // );
    }
}
