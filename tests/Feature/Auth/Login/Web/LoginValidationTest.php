<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Web;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_email_and_password(): void
    {
        $this->postJson('/api/auth/web/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function email_must_be_a_valid_email(): void
    {
        $this->postJson('/api/auth/web/login', [
            'email' => 'not-an-email',
            'password' => 'anything',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function remember_me_is_optional_boolean(): void
    {
        $this->postJson('/api/auth/web/login', [
            'email' => 'john.doe@gmail.com',
            'password' => 'StrongPass1!',
            'remember' => 'yes',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['remember']);
    }
}
