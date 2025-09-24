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
}
