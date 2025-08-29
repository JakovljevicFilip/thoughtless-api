<?php
declare(strict_types=1);

namespace Feature\Auth\EmailVerification;

use App\Models\User;
use App\Tokens\EmailVerification\EmailVerificationTokenFactory;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function link_has_expired(): void
    {
        $user = User::factory()->unverified()->create();

        $token = $this->makeFrontendToken($user, now()->subMinute());

        $this->postJson('/api/email/verify', [
            'id'    => (string) $user->id,
            'token' => $token,
        ])->assertStatus(422);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertGuest();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_confirm_their_email_address(): void
    {
        Event::fake();
        $user = User::factory()->unverified()->create();

        $token = $this->makeFrontendToken($user, now()->addHour());

        $this->postJson('/api/email/verify', [
            'id'    => (string) $user->id,
            'token' => $token,
        ])->assertOk();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_is_logged_in_once_validation_passes(): void
    {
        $user = User::factory()->unverified()->create();
        $token = $this->makeFrontendToken($user, now()->addHour());

        $this->postJson('/api/email/verify', [
            'id'    => (string) $user->id,
            'token' => $token,
        ])->assertOk();

        $this->assertAuthenticatedAs($user->fresh());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function already_verified_user_passes_verification(): void
    {
        Event::fake();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $this->makeFrontendToken($user, now()->addHour());

        $this->actingAs($user)->postJson('/api/email/verify', [
            'id'    => (string) $user->id,
            'token' => $token,
        ])->assertOk();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertNotDispatched(Verified::class);
    }

    private function makeFrontendToken(User $user, Carbon $expiresAt): string
    {
        return app(EmailVerificationTokenFactory::class)->make($user, $expiresAt);
    }

}
