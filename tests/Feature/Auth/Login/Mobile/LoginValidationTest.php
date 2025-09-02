<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
