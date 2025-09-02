<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_email_and_password(): void
    {
        $this->postJson('/api/auth/mobile/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function email_must_be_a_valid_email(): void
    {
        $this->postJson('/api/auth/mobile/login', [
            'email' => 'not-an-email',
            'password' => 'anything',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function device_name_is_optional_string(): void
    {
        $this->postJson('/api/auth/mobile/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
            'device_name' => ['phone'], // invalid type
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['device_name']);
    }
}
