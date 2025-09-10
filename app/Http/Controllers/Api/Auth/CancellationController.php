<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Auth\CancellationActionContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CancellationRequest;
use Illuminate\Http\JsonResponse;

final class CancellationController extends Controller
{
    public function __construct(private readonly CancellationActionContract $cancel) {}
    public function store(CancellationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->cancel->execute($data['user_id'], $data['token']);
        return response()->json([]);
    }
}
