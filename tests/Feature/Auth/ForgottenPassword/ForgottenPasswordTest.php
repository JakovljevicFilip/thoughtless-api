<?php

namespace Tests\Feature\Auth\ForgottenPassword;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgottenPasswordTest extends TestCase
{
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
        Mail::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'If that email exists, a password reset link has been sent.'
            ]);

        Mail::assertNothingSent();
    }
}
