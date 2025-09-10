<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Cancellation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_and_user_id_are_required(): void
    {
        $this->postJson('/api/user/cancel', [])->assertStatus(422)
            ->assertJsonValidationErrors(['user_id','token']);
    }
}
