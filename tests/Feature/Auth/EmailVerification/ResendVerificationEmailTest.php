<?php
declare(strict_types=1);

namespace Feature\Auth\EmailVerification;

use App\Models\User;
use App\Tokens\EmailVerification\EmailVerificationTokenFactory;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function link_can_be_resent(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])
            ->assertOk()
            ->assertJson(['message' => 'Verification link resent.']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function multiple_links_cannot_be_resent_within_10_minutes(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        Carbon::setTestNow(now()->addMinutes(10)->addSecond());
        // 1st resend
        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])->assertOk();

        // 2nd resend - within 10 minutes since first request.
        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])->assertStatus(429);

        // 3rd resend - after 10 minutes since 2nd request
        Carbon::setTestNow(now()->addMinutes(10)->addSecond());
        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])->assertOk();

        Notification::assertSentToTimes($user, VerifyEmail::class, 2);
        Carbon::setTestNow();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_resend_if_user_not_found(): void
    {
        $this->postJson('/api/email/resend', [
            'id' => (string) \Illuminate\Support\Str::uuid(),
        ])->assertStatus(422)
        ->assertJsonValidationErrors('id');
    }

    public function cannot_resend_if_user_already_verified(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])->assertStatus(422)
        ->assertJsonValidationErrors('id');

        Notification::assertNothingSent();
    }

    public function unverified_user_can_resend(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/email/resend', [
            'id' => (string) $user->id,
        ])->assertOk()
            ->assertJson(['message' => 'Verification link resent.']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
