<?php

namespace Tests\Feature\Auth\ForgottenPassword;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgottenPasswordRequestTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_an_email_address()
    {
        $response = $this->postJson('/api/user/forgot-password', [
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_a_valid_email_address()
    {
        $response = $this->postJson('/api/user/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_does_not_send_email_for_nonexistent_account_but_returns_generic_message()
    {
        Notification::fake();

        $response = $this->postJson('/api/user/forgot-password', [
            'email' => 'nonexistent@gmail.com',
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
            'email' => 'jane@gmail.com',
        ]);

        $response = $this->postJson('/api/user/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If that email exists, a password reset link has been sent.'
            ]);

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_valid_password_token()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'jane@gmail.com',
        ]);

        $this->postJson('/api/user/forgot-password', [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_the_custom_reset_password_email_template()
    {
        Notification::fake();
        config(['app.frontend_url' => 'http://localhost']);

        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'jane@gmail.com',
        ]);

        $this->postJson('/api/user/forgot-password', ['email' => $user->email])
            ->assertStatus(200);

        Notification::assertSentTo($user, ResetPasswordNotification::class);

        $sent = Notification::sent($user, ResetPasswordNotification::class);
        $this->assertNotEmpty($sent, 'No ResetPasswordNotification captured.');

        /** @var ResetPasswordNotification $notification */
        $notification = $sent->first();

        $mail = $notification->toMail($user);

        $expectedSubject = "Hey Jane, did you forget your password?";
        $this->assertSame($expectedSubject, $mail->subject);

        if (is_array($mail->view)) {
            $this->assertSame('emails.auth.password.reset..reset', $mail->view['html']);
            $this->assertSame('emails.auth.password.reset.reset_plain', $mail->view['text']);
        } else {
            $this->assertSame('emails.auth.password.reset.reset', $mail->view);
        }

        $data = $mail->viewData ?? [];
        $expectedUrl = rtrim(config('app.frontend_url'), '/') .
            '/reset-password?token=' . $notification->token .
            '&email=' . urlencode($user->email);

        $this->assertSame($expectedUrl, $data['resetUrl'] ?? null);
        $this->assertSame(config('app.suite_name'), $data['suite'] ?? null);

        $html = view(is_array($mail->view) ? $mail->view['html'] : $mail->view, $data)->render();
        $this->assertStringContainsString("We've received a request", $html);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_actually_sends_a_password_reset_email_to_the_users_address()
    {
        $user = User::factory()->create(['email' => 'jane@gmail.com']);

        $response = $this->postJson('/api/user/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
    }
}
