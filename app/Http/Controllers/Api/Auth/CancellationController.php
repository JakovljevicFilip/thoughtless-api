<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CancellationRequest;
use Illuminate\Http\JsonResponse;

final class CancellationController extends Controller
{
    public function store(CancellationRequest $request): JsonResponse
    {
        return response()->json([]);
    }
}
