<?php

declare(strict_types=1);

namespace Feature\Thought;

use Database\Seeders\Test\ThoughtSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListThoughtsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function returns_empty_list_when_no_data(): void
    {
        $res = $this->getJson('/api/thoughts');

        $res->assertOk();
        $this->assertSame([], $res->json('data'));
        $this->assertArrayNotHasKey('meta', $res->json());
        $this->assertArrayNotHasKey('links', $res->json());
    }

    #[Test]
    public function lists_thoughts_sorted_newest_first_without_pagination(): void
    {
        $this->seed(ThoughtSeeder::class);

        $res = $this->getJson('/api/thoughts');

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [['id','content','created_at','updated_at']],
            ]);

        $thoughts = $res->json('data');
        $this->assertCount(3, $thoughts);

        $dates = array_column($thoughts, 'created_at');
        $sorted = $dates;
        rsort($sorted);
        $this->assertSame($sorted, $dates);
    }
}
