<?php
declare(strict_types=1);

namespace App\Tokens\EmailVerification;

use Carbon\CarbonImmutable as Carbon;
use InvalidArgumentException;

final readonly class EmailVerificationToken
{
    public function __construct(
        public string $hash,
        public string $signature,
        public Carbon $expiresAt,
    ) {}

    public static function parse(string $encoded): self
    {
        $raw = base64_decode($encoded, true);
        if ($raw === false) {
            throw new InvalidArgumentException('Invalid verification token.');
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['hash'], $data['sig'], $data['exp'])) {
            throw new InvalidArgumentException('Invalid verification token.');
        }

        return new self(
            hash: (string) $data['hash'],
            signature: (string) $data['sig'],
            expiresAt: Carbon::createFromTimestamp((int) $data['exp']),
        );
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }

    public function toBase64(): string
    {
        return base64_encode(json_encode([
            'hash' => $this->hash,
            'sig'  => $this->signature,
            'exp'  => $this->expiresAt->getTimestamp(),
        ]));
    }
}
