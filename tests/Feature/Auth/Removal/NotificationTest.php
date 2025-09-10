<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Removal;

use App\Mail\AccountRemovalScheduledMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class NotificationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_is_notified_about_impending_deletion(): void
    {
        Mail::fake();
        Config::set('auth.deletion_grace_hours', 24);

        $user = User::factory()->create(['password' => bcrypt('pw')]);

        $this->actingAs($user)
            ->postJson('/api/user/remove', ['password' => 'pw'])
            ->assertOk();

        Mail::assertSent(AccountRemovalScheduledMail::class, function ($mailable) use ($user) {
            /** @var AccountRemovalScheduledMail $mailable */
            $this->assertSame('Your account will be deleted in 24 hours', $mailable->subject);
            $this->assertSame($user->id, $mailable->user->id);

            $this->assertEquals('emails.auth.removal.removal', $mailable->view);
            $this->assertEquals('emails.auth.removal.removal_plain', $mailable->textView);

            return true;
        });
    }
}
