<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendVerificationRequest;
use Illuminate\Http\JsonResponse;

final class ResendVerificationController extends Controller
{
    public function __invoke(ResendVerificationRequest $request): JsonResponse
    {
        $user = $request->targetUser();

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link resent.']);
    }
}
