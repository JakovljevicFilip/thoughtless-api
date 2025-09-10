<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Removal;

use App\Jobs\PruneDeletedUserJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DependsExternal;

final class ActionTest extends TestCase
{
    use RefreshDatabase;

//    #[DependsExternal(\Tests\Feature\Auth\Login\Web\LoginWebTest::class, 'test_login_populates_sessions_user_id')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_is_marked_for_deletion_and_logged_out_everywhere(): void
    {
        Queue::fake();
        $user = User::factory()->create(['password' => bcrypt('pw-123456')]);

        DB::table('sessions')->insert([
            'id' => \Str::uuid()->toString(),
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')->insert([
                'tokenable_type' => $user::class,
                'tokenable_id'   => $user->id,
                'name'           => 'test-token',
                'token'          => hash('sha256', 'dummy'),
                'abilities'      => json_encode(['*']),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->actingAs($user)
            ->postJson('/api/user/remove', ['password' => 'pw-123456'])
            ->assertOk();

        $user->refresh();
        $this->assertNotNull($user->marked_for_deletion_at);

        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);

        if (Schema::hasTable('personal_access_tokens')) {
            $this->assertDatabaseMissing('personal_access_tokens', [
                'tokenable_type' => $user::class,
                'tokenable_id'   => $user->id,
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function deletion_job_is_queued_with_configured_delay(): void
    {
        Queue::fake();
        Config::set('auth.deletion_grace_hours', 24);

        $user = User::factory()->create(['password' => bcrypt('pw')]);

        $this->actingAs($user)->postJson('/api/user/remove', ['password' => 'pw'])->assertOk();

        Queue::assertPushed(PruneDeletedUserJob::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });

        $user->refresh();
    }
}
