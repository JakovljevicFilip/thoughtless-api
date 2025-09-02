<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutMobileController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        return response()->json(null, 204);
    }
}
