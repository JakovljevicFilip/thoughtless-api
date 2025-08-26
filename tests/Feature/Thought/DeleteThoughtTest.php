<?php

namespace Feature\Thought;

use Database\Seeders\Test\ThoughtSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteThoughtTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_non_existing_thought_test(): void
    {
        $nonExistingUuid = Str::uuid()->toString();

        $this->deleteJson("/api/thoughts/{$nonExistingUuid}")
            ->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_existing_thought_test(): void
    {
        $this->seed(ThoughtSeeder::class);

        $this->deleteJson('/api/thoughts/52033e3f-8566-453d-9f1b-000000000002')
            ->assertNoContent();

        $this->assertDatabaseMissing('thoughts', [
            'id' => '52033e3f-8566-453d-9f1b-000000000002',
        ]);
    }
}
