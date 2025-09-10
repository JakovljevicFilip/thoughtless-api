<?php

namespace Feature\Thought;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThoughtTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function content_is_a_required_field_test()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thoughts', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guests_cannot_create_thoughts_test()
    {
        $response = $this->postJson('/api/thoughts', [
            'content' => 'Guest thought',
        ]);

        $response->assertStatus(401);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_users_can_create_thoughts_test()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/thoughts', [
            'content' => 'User thought',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('thoughts', [
            'content' => 'User thought',
            'user_id' => $user->id,
        ]);
    }
}
