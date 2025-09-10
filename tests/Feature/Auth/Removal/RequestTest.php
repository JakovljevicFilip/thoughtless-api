<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Removal;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
