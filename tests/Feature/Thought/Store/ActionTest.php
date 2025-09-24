<?php

declare(strict_types=1);

namespace Tests\Feature\Thought\Store;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ActionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_a_single_thought_through_the_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => 'Offline synced thought',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('thoughts', [
            'content' => 'Offline synced thought',
            'user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_multiple_thoughts_in_one_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            [
                'id' => (string) Str::uuid(),
                'content' => 'First offline thought',
                'created_at' => now()->format('Y-m-d H:i:s'),
            ],
            [
                'id' => (string) Str::uuid(),
                'content' => 'Second offline thought',
                'created_at' => now()->subMinute()->format('Y-m-d H:i:s'),
            ],
        ];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('thoughts', [
            'content' => 'First offline thought',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('thoughts', [
            'content' => 'Second offline thought',
            'user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_the_created_models()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => 'Returned model test',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'content' => 'Returned model test',
        ]);
    }
}
