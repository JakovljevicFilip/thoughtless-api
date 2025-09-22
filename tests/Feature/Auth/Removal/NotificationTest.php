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

        Mail::assertSent(AccountRemovalScheduledMail::class, function (AccountRemovalScheduledMail $mailable) use ($user) {
            $this->assertSame('Your account will be deleted in 24 hours', $mailable->envelope()->subject);
            $this->assertSame('emails.auth.removal.removal', $mailable->content()->view);
            $this->assertSame('emails.auth.removal.removal_plain', $mailable->content()->text);
            $this->assertTrue($mailable->hasTo($user->email));
            $this->assertSame($user->id, $mailable->user->id);

            $html = $mailable->render();
            $this->assertStringContainsString('/cancel-removal', $html);
            return true;
        });
    }
}
