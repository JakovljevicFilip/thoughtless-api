<?php
declare(strict_types=1);

namespace App\Tokens\EmailVerification;

use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\URL;

final class EmailVerificationTokenFactory
{
    public function make(User $user, CarbonInterface $expiresAt): string
    {
        $imm = $expiresAt instanceof CarbonImmutable
            ? $expiresAt
            : CarbonImmutable::instance($expiresAt);

        $hash = sha1($user->getEmailForVerification());

        $signed = URL::temporarySignedRoute(
            'verification.verify',
            $imm,
            ['id' => (string) $user->id, 'hash' => $hash]
        );

        parse_str(parse_url($signed, PHP_URL_QUERY) ?: '', $query);
        $signature = (string) ($query['signature'] ?? '');

        return (new EmailVerificationToken($hash, $signature, $imm))->toBase64();
    }
}
