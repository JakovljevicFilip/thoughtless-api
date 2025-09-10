<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Cancellation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_id_is_required(): void
    {
        $response = $this->postJson('/api/user/cancel', []);
        $response->assertStatus(422)->assertJsonValidationErrors('user_id');
    }
}
