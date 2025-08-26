<?php

namespace Feature\Thought;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThoughtTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function content_is_a_required_field_test()
    {
        $response = $this->postJson('/api/thoughts', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function content_can_be_created_test()
    {
        $response = $this->postJson('/api/thoughts', [
            'content' => 'My first thought.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('thoughts', [
            'content' => 'My first thought.',
        ]);
    }
}
