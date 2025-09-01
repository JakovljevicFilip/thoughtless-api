<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Web\LoginWebRequest;
use Illuminate\Http\JsonResponse;

final class LoginWebController extends Controller
{
    public function store(LoginWebRequest $request): JsonResponse
    {
        $request->validated();

        return response()->json([], 201);
    }
}
