<?php

declare(strict_types=1);

namespace Tests\Feature\Thought\Store;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function guests_cannot_store_thoughts()
    {
        $response = $this->postJson('/api/thought/store', []);

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_are_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thought/store', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_have_to_be_an_array()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => 'not-an-array',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_must_have_ids()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'content' => 'Valid content',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.id');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thought_ids_must_be_digits()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => 'abc', // invalid
            'content' => 'Valid content',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.id');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_must_have_content()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.content');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_must_have_created_at()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => 'Valid content',
            'created_at' => 'invalid-date',
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.created_at');
    }
}
