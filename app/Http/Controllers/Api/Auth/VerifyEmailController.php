<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Tokens\EmailVerification\EmailVerificationService;
use Illuminate\Http\JsonResponse;

final class VerifyEmailController extends Controller
{
    public function __invoke(
        VerifyEmailRequest $request,
        EmailVerificationService $service
    ): JsonResponse {
        $user = $request->userForVerification();
        $service->markVerifiedAndLogin($user);

        return response()->json([
            'message' => $user->wasChanged('email_verified_at')
                ? 'Email verified successfully.'
                : 'Email already verified.',
        ]);
    }
}
