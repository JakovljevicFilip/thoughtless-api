<?php
declare(strict_types=1);

namespace Tests\Feature\Auth\Cancellation;

use App\Jobs\PruneDeletedUserJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function cancels_removal_via_token_and_user_id(): void
    {
        Mail::fake();
        Queue::fake();
        Config::set('auth.deletion_grace_hours', 24);

        $user = User::factory()->create(['password' => bcrypt('pw'), 'marked_for_deletion_at' => now()]);
        $plain = Str::random(64);
        DB::table('deletion_cancellation_tokens')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'token_hash' => password_hash($plain, PASSWORD_BCRYPT),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->postJson('/api/user/cancel-removal', ['user_id' => $user->id, 'token' => $plain])
            ->assertOk()->assertJsonFragment(['message' => 'Account removal canceled.']);

        $user->refresh();
        $this->assertNull($user->marked_for_deletion_at);

        $this->assertDatabaseMissing('deletion_cancellation_tokens', ['user_id' => $user->id]);
        Queue::assertNothingPushed(PruneDeletedUserJob::class); // already scheduled job will no-op due to guard
    }
}
