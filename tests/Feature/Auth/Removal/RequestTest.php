<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Removal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    use RefreshDatabase;

    private function postRemove(array $payload = [], ?User $as = null): TestResponse
    {
        if ($as) {
            $this->actingAs($as);
        }
        return $this->postJson('/api/user/remove', $payload);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_request_account_removal(): void
    {
        $response = $this->postRemove(['password' => 'irrelevant']);
        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_is_required(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123!')]);

        $response = $this->postRemove(['password' => ''], $user);
        $response->assertStatus(422)->assertJsonValidationErrors('password');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_must_match_current_users_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123!')]);

        $response = $this->postRemove(['password' => 'wrong-password'], $user);
        $response->assertStatus(422)->assertJsonFragment([
            'message' => 'The provided password is incorrect.',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_valid_password_the_request_proceeds(): void
    {
        Queue::fake();
        $user = User::factory()->create(['password' => bcrypt('secret123!')]);

        $response = $this->postRemove(['password' => 'secret123!'], $user);
        $response->assertOk()->assertJsonFragment([
            'message' => 'Account deletion scheduled.',
        ]);

        $user->refresh();
        $this->assertNotNull($user->marked_for_deletion_at);
    }
}
