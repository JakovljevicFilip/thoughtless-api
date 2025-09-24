<?php

declare(strict_types=1);

namespace Tests\Feature\Thought\Store;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function guests_cannot_store_thoughts(): void
    {
        $response = $this->postJson('/api/thought/store', []);

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_are_required(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thought/store', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_have_to_be_an_array(): void
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
    public function thoughts_must_have_ids(): void
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
    public function thought_ids_must_be_valid_uuids(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => 'not-a-uuid',
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
    public function thoughts_must_have_content(): void
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
    public function thoughts_must_have_created_at(): void
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_array_cannot_be_empty(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_content_cannot_be_empty_string(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => '',
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.content');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_must_have_created_at_field(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => 'Valid content',
            // no created_at
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.created_at');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function thoughts_created_at_must_match_exact_format(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [[
            'id' => (string) Str::uuid(),
            'content' => 'Valid content',
            // valid date, wrong format (d-m-Y instead of Y-m-d)
            'created_at' => now()->format('d-m-Y H:i:s'),
        ]];

        $response = $this->postJson('/api/thought/store', [
            'thoughts' => $payload,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('thoughts.0.created_at');
    }
}
