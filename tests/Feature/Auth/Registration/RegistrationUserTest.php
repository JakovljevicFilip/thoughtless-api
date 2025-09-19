<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Registration;

use App\Mail\VerificationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'john.doe@gmail.com',
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
            'email' => 'john.doe@gmail.com',
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
        Mail::fake();

        $payload = [
            'first_name' => 'Alice',
            'last_name'  => 'Verify',
            'email' => 'john.doe@gmail.com',
            'password'   => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ];

        $this->postJson('/api/user/register', $payload)->assertStatus(201);

        $user = User::where('email', $payload['email'])->firstOrFail();

        Mail::assertSent(VerificationMail::class, function (VerificationMail $mail) use ($user) {
            $rendered = (string) $mail->render();

            $feUrl = preg_quote(rtrim(config('app.frontend_url'), '/'), '/');
            $email = preg_quote(urlencode($user->email), '/');

            // Path params instead of query params
            $pattern = "#{$feUrl}/verify/{$email}/[^\"' ]+#";

            $this->assertMatchesRegularExpression($pattern, $rendered);

            $expectedLogo = asset('icons/favicon-512x512.png');
            $this->assertStringContainsString($expectedLogo, $rendered);

            return true;
        });
    }
}
