<?php
declare(strict_types=1);

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\LoginMobileAction;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginMobileActionTest extends TestCase
{
    use RefreshDatabase;

    private LoginMobileAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = $this->app->make(LoginMobileAction::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_invalid_credentials_for_bad_password(): void
    {
        User::factory()->create([
            'email'             => 'jane@example.com',
            'password'          => Hash::make('Correct#123'),
            'email_verified_at' => now(),
        ]);

        $this->expectException(InvalidCredentials::class);
        $this->expectExceptionMessage('Invalid credentials.');
        $this->expectExceptionCode(401);

        $this->action->execute('jane@example.com', 'wrong', 'Pixel 7');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_email_not_verified_for_unverified_user(): void
    {
        User::factory()->create([
            'email'             => 'alice@example.com',
            'password'          => Hash::make('StrongPass1!'),
            'email_verified_at' => null,
        ]);

        $this->expectException(EmailNotVerified::class);
        $this->expectExceptionMessage('Please verify your email before continuing.');
        $this->expectExceptionCode(403);

        $this->action->execute('alice@example.com', 'StrongPass1!', 'Pixel 7');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_token_and_user_on_success(): void
    {
        $user = User::factory()->create([
            'first_name'        => 'John',
            'last_name'         => 'Doe',
            'email'             => 'john@example.com',
            'password'          => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $res = $this->action->execute('john@example.com', 'StrongPass1!', 'Pixel 7');

        $this->assertIsArray($res);
        $this->assertArrayHasKey('access_token', $res);
        $this->assertArrayHasKey('token_type', $res);
        $this->assertArrayHasKey('expires_in', $res);
        $this->assertArrayHasKey('user', $res);

        $this->assertIsString($res['access_token']);
        $this->assertSame('Bearer', $res['token_type']);
        $this->assertNull($res['expires_in']); // non-expiring

        $this->assertSame([
            'id'         => (string) $user->id,
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
        ], $res['user']);
    }
}
