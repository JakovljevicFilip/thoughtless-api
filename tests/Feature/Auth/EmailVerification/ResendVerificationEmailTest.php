<?php
declare(strict_types=1);

namespace Feature\Auth\EmailVerification;

use App\Mail\VerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function link_can_be_resent(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])
            ->assertOk()
            ->assertJson(['message' => 'Verification link resent.']);

        Mail::assertSent(VerificationMail::class, function (VerificationMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function maximum_3_links_can_be_sent_within_10_minutes(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        // 1st resend
        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])->assertOk();

        // 2nd resend - within 10 minutes since first request → should pass
        Carbon::setTestNow(now()->addMinutes(5));
        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])->assertOk();

        // 3rd resend - within 10 minutes since first request → should pass
        Carbon::setTestNow(now()->addMinutes());
        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])->assertOk();

        // 4th resend - within 10 minutes since first request → should fail
        Carbon::setTestNow(now()->addMinutes());
        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])->assertStatus(429);

        // 3rd resend - after 10 minutes → should succeed
        Carbon::setTestNow(now()->addMinutes(11));
        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])->assertOk();

        Mail::assertSent(VerificationMail::class, 4);
        Carbon::setTestNow();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_resend_if_user_not_found(): void
    {
        $this->postJson('/api/email/resend', [
            'email' => 'unknown@mail.com',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_resend_if_user_already_verified(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        Mail::assertNothingSent();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unverified_user_can_resend(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/email/resend', [
            'email' => $user->email,
        ])
            ->assertOk()
            ->assertJson(['message' => 'Verification link resent.']);

        Mail::assertSent(VerificationMail::class, function (VerificationMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
