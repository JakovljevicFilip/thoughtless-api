<?php

declare(strict_types=1);

namespace Feature\Thought;

use App\Models\Thought;
use App\Models\User;
use Database\Seeders\Test\ThoughtSeeder;
use Database\Seeders\Test\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ListThoughtsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function returns_empty_list_when_no_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->getJson('/api/thoughts');

        $res->assertOk();
        $this->assertSame([], $res->json('data'));
        $this->assertArrayNotHasKey('meta', $res->json());
        $this->assertArrayNotHasKey('links', $res->json());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function users_cannot_list_other_users_thoughts(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ThoughtSeeder::class);

        $existingThought = Thought::first();
        $existingUser = $existingThought->user;

        $newUser = User::factory()->create();
        $newThought = $newUser->thoughts()->create([
            'id'      => Str::uuid()->toString(),
            'content' => "New user's thought.",
        ]);

        $this->actingAs($existingUser);

        $res = $this->getJson('/api/thoughts');
        $newUserThoughts = $res->json('data');
        $this->assertCount(Thought::all()->count() - 1, $newUserThoughts);
        $this->assertNotContains($newThought->content, $newUserThoughts);

        $this->actingAs($newUser);

        $res = $this->getJson('/api/thoughts');
        $existingUserThoughts = $res->json('data');
        $this->assertCount(1, $existingUserThoughts);
        $this->assertNotContains($newThought->content, $existingUserThoughts);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lists_thoughts_sorted_newest_first_without_pagination(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(ThoughtSeeder::class);

        $user = Thought::first()->user;
        $this->actingAs($user);

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
