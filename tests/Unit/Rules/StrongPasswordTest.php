<?php
declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class StrongPasswordTest extends TestCase
{
    private function validate(string $password): bool
    {
        $validator = Validator::make(
            ['password' => $password],
            ['password' => [new StrongPassword]]
        );

        return $validator->passes();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_passes_with_a_strong_password(): void
    {
        $this->assertTrue($this->validate('Zy9$' . bin2hex(random_bytes(4))));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_if_it_is_shorter_than_8_characters(): void
    {
        $this->assertFalse($this->validate('Aa1!'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_without_uppercase_letter(): void
    {
        $this->assertFalse($this->validate('weakpass1!'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_without_lowercase_letter(): void
    {
        $this->assertFalse($this->validate('WEAKPASS1!'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_without_number(): void
    {
        $this->assertFalse($this->validate('NoNumbers!'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_without_symbol(): void
    {
        $this->assertFalse($this->validate('NoSymbols1'));
    }
}
