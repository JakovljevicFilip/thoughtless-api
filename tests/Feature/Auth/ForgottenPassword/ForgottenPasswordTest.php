<?php

namespace Tests\Feature\Auth\ForgottenPassword;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class ForgottenPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_an_email_address()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_a_valid_email_address()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_send_email_for_nonexistent_account_but_returns_generic_message()
    {
        Notification::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If that email exists, a password reset link has been sent.'
            ]);

        Notification::assertNothingSent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_a_forgotten_password_link_if_email_exists()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'jane@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If that email exists, a password reset link has been sent.'
            ]);

        Notification::assertSentTo(
            $user,
            ResetPassword::class
        );
    }
}
