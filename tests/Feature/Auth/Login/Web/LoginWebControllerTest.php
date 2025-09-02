<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Web;

use App\Contracts\Auth\LoginWebContract;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class LoginWebControllerTest extends TestCase
{
    use RefreshDatabase;

    private function csrf(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertNoContent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function maps_invalid_credentials_to_401(): void
    {
        $this->csrf();

        $mock = Mockery::mock(LoginWebContract::class);
        $mock->shouldReceive('execute')->andThrow(new InvalidCredentials());
        $this->app->instance(LoginWebContract::class, $mock);

        $this->postJson('/api/auth/web/login', [
            'email' => 'x@example.com',
            'password' => 'nope',
        ])->assertStatus(401)->assertJson(['message' => 'Invalid credentials.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function maps_email_not_verified_to_403(): void
    {
        $this->csrf();

        $mock = Mockery::mock(LoginWebContract::class);
        $mock->shouldReceive('execute')->andThrow(new EmailNotVerified());
        $this->app->instance(LoginWebContract::class, $mock);

        $this->postJson('/api/auth/web/login', [
            'email' => 'y@example.com',
            'password' => 'ok',
        ])->assertStatus(403)->assertJson(['message' => 'Please verify your email before continuing.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_user_block_with_names_on_success(): void
    {
        $this->csrf();

        $user = User::factory()->make([
            'id'         => '01990a69-a946-73e3-9049-6e5aa24131df',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
        ]);

        $mock = Mockery::mock(LoginWebContract::class);
        $mock->shouldReceive('execute')->andReturn($user);
        $this->app->instance(LoginWebContract::class, $mock);

        $this->postJson('/api/auth/web/login', [
            'email' => 'john@example.com',
            'password' => 'StrongPass1!',
        ])->assertOk()->assertJson([
            'message' => 'Logged in.',
            'user' => [
                'id'         => (string) $user->id,
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john@example.com',
            ],
        ]);
    }
}
