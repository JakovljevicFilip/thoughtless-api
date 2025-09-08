<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\Auth\AlreadyVerified;
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
        $wasJustVerified = $service->markVerifiedAndLogin($user);

        if (! $wasJustVerified) {
            throw new AlreadyVerified();
        }

        return response()->json([
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'token'      => $user->createToken('auth_token')->plainTextToken,
            'message'    => 'Email verified successfully.',
        ]);
    }
}
