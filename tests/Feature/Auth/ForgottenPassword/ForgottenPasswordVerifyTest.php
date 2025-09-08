<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\ForgottenPassword;

use App\Actions\Auth\VerifyForgotForgotPasswordAction;
use App\Exceptions\Auth\ExpiredResetLink;
use App\Exceptions\Auth\InvalidResetLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class ForgottenPasswordVerifyTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_path(): void
    {
        $user  = User::factory()->create(['email' => 'jane@example.com']);
        $plain = Password::createToken($user);

        $result = app(VerifyForgotForgotPasswordAction::class)->execute($user->email, $plain);

        $this->assertSame('Valid password reset link.', $result['message']);
    }

    public function test_invalid_token(): void
    {
        $this->expectException(InvalidResetLink::class);

        $user = User::factory()->create(['email' => 'jane@example.com']);
        Password::createToken($user); // create a real one but pass wrong string

        app(VerifyForgotForgotPasswordAction::class)->execute($user->email, 'nope');
    }

    public function test_expired_token(): void
    {
        $this->expectException(ExpiredResetLink::class);

        $user  = User::factory()->create(['email' => 'jane@example.com']);
        $plain = Password::createToken($user);

        $minutes = (int) (config('auth.passwords.users.expire') ?? 60);
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update(['created_at' => now()->subMinutes($minutes + 1)]);

        app(VerifyForgotForgotPasswordAction::class)->execute($user->email, $plain);
    }
}
