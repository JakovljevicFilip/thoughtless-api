<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Mobile\MeRequest;
use Illuminate\Http\JsonResponse;

final class MeController extends Controller
{
    public function __invoke(MeRequest $request): JsonResponse
    {
        $u = $request->user();

        return response()->json([
            'id'    => (string) $u->getKey(),
            'email' => $u->email,
            'first_name' => $u->first_name,
            'last_name' => $u->last_name,
        ]);
    }
}
