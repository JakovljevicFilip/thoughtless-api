<?php

namespace Feature\Thought;

use App\Models\Thought;
use App\Models\User;
use Database\Seeders\Test\ThoughtSeeder;
use Database\Seeders\Test\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteThoughtTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_non_existing_thought_test(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $nonExistingUuid = Str::uuid()->toString();

        $this->deleteJson("/api/thoughts/{$nonExistingUuid}")
            ->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cannot_delete_other_users_thought(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ThoughtSeeder::class);

        $thought = Thought::first();
        $user = User::factory()->create();
        $this->actingAs($user);

        $thoughtId = $thought->id;

        $this->deleteJson('/api/thoughts/' . $thoughtId)
            ->assertForbidden();

        $this->assertDatabaseHas('thoughts', [
            'id' => $thoughtId,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_existing_thought_test(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ThoughtSeeder::class);

        $thought = Thought::first();
        $this->actingAs($thought->user);

        $thoughtId = $thought->id;

        $this->deleteJson('/api/thoughts/' . $thoughtId)
            ->assertNoContent();

        $this->assertDatabaseMissing('thoughts', [
            'id' => $thoughtId,
        ]);
    }
}
