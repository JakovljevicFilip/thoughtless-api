<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Login\Mobile;

use App\Contracts\Auth\LoginMobileContract;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class LoginMobileControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_401_when_action_throws_invalid_credentials(): void
    {
        $mock = Mockery::mock(LoginMobileContract::class);
        $mock->shouldReceive('execute')->andThrow(new InvalidCredentials());
        $this->app->instance(LoginMobileContract::class, $mock);

        $this->postJson('/api/auth/mobile/login', [
            'email' => 'x@gmail.com', 'password' => 'x', 'device_name' => 'dev',
        ])->assertStatus(401)->assertJson(['message' => 'Invalid credentials.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_403_when_action_throws_email_not_verified(): void
    {
        $mock = Mockery::mock(LoginMobileContract::class);
        $mock->shouldReceive('execute')->andThrow(new EmailNotVerified());
        $this->app->instance(LoginMobileContract::class, $mock);

        $this->postJson('/api/auth/mobile/login', [
            'email' => 'y@gmail.com', 'password' => 'ok', 'device_name' => 'dev',
        ])->assertStatus(403)->assertJson(['message' => 'Please verify your email before continuing.']);
    }
}
