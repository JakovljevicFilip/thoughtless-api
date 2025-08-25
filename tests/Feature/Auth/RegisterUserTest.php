<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
