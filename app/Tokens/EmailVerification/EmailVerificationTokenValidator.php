<?php
declare(strict_types=1);

namespace App\Tokens\EmailVerification;

use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\URL;

final class EmailVerificationTokenValidator
{
    public function assertValid(string $id, User $user, string $encoded): EmailVerificationToken
    {
        try {
            $token = EmailVerificationToken::parse($encoded);
        } catch (\InvalidArgumentException) {
            throw new DomainException('invalid-token');
        }

        if ($token->isExpired()) {
            throw new DomainException('expired');
        }

        if (! $this->emailHashMatches($user, $token)) {
            throw new DomainException('bad-hash');
        }

        if (! $this->signatureValid($id, $token)) {
            throw new DomainException('bad-signature');
        }

        return $token;
    }

    public function emailHashMatches(User $user, EmailVerificationToken $token): bool
    {
        return hash_equals(
            sha1($user->getEmailForVerification()),
            $token->hash
        );
    }

    public function signatureValid(string $email, EmailVerificationToken $token): bool
    {
        $expectedUrl = URL::temporarySignedRoute(
            'verification.verify',
            $token->expiresAt,
            ['email' => (string) $email, 'hash' => $token->hash]
        );

        parse_str(parse_url($expectedUrl, PHP_URL_QUERY) ?: '', $query);
        $expectedSig = (string) ($query['signature'] ?? '');

        return hash_equals($expectedSig, $token->signature);
    }
}
