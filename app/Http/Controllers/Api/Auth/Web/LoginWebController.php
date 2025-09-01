<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Web\LoginWebRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class LoginWebController extends Controller
{
    public function store(LoginWebRequest $request): JsonResponse
    {
        $user = $request->getAuthenticatedUser();
        $remember = $request->boolean('remember');

        Auth::guard('web')->login($user, $remember);

        return response()->json([
            'user' => [
                'id'         => (string) $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
            ],
        ]);
    }
}
