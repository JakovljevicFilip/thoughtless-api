<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Cancellation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    use RefreshDatabase;

    private function makeToken(User $user, ?\DateTimeInterface $expires = null): array
    {
        $plain = Str::random(64);
        DB::table('deletion_cancellation_tokens')->insert([
            'id'         => (string) Str::uuid(),
            'user_id'    => $user->id,
            'token_hash' => password_hash($plain, PASSWORD_BCRYPT),
            'expires_at' => ($expires ?? now()->addHours(24)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return [$plain, $user->id];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_and_user_id_are_required(): void
    {
        $this->postJson('/api/user/cancel-removal', [])->assertStatus(422)
            ->assertJsonValidationErrors(['user_id','token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_must_match_to_the_existing_tokens(): void
    {
        $user = User::factory()->create(['marked_for_deletion_at' => now()]);
        $this->makeToken($user, now());

        $this->postJson('/api/user/cancel-removal', ['user_id' => $user->id, 'token' => Str::random(64)])
            ->assertStatus(422)->assertJsonFragment(['message' => 'This cancellation link is invalid.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_can_expire(): void
    {
        $user = User::factory()->create(['marked_for_deletion_at' => now()]);
        [$plain, $uid] = $this->makeToken($user, now()->subHour());

        $this->postJson('/api/user/cancel-removal', ['user_id' => $uid, 'token' => $plain])
            ->assertStatus(422)->assertJsonFragment(['message' => 'This cancellation link has expired.']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_cancel_if_user_not_marked_for_deletion(): void
    {
        $user = User::factory()->create(['marked_for_deletion_at' => null]);
        [$plain, $uid] = $this->makeToken($user);

        $this->postJson('/api/user/cancel-removal', ['user_id' => $uid, 'token' => $plain])
            ->assertStatus(409)->assertJsonFragment(['message' => 'No deletion is scheduled.']);
    }
}
