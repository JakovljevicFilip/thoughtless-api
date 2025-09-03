<?php
declare(strict_types=1);

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\LoginWebAction;
use App\Exceptions\Auth\EmailNotVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoginWebActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Correct#123'),
            'email_verified_at' => now(),
        ]);

        $this->expectException(InvalidCredentials::class);
        app(LoginWebAction::class)->execute('jane@example.com', 'wrong', false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_email_not_verified(): void
    {
        User::factory()->create([
            'email' => 'alice@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => null,
        ]);

        $this->expectException(EmailNotVerified::class);
        app(LoginWebAction::class)->execute('alice@example.com', 'StrongPass1!', false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_in_and_returns_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('StrongPass1!'),
            'email_verified_at' => now(),
        ]);

        $returned = app(LoginWebAction::class)->execute('john@example.com', 'StrongPass1!', true);

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, $returned->id);
    }
}
