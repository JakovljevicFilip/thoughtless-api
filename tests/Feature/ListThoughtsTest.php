<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListThoughtsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function returns_empty_list_when_no_data(): void
    {
        $res = $this->getJson('/api/thoughts');

        $res->assertOk();
        $this->assertSame([], $res->json());
    }
}
