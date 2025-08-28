<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Registration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
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
}
