<?php

namespace App\Http\Controllers\Api\Thoughts\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thoughts\StoreRequest;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        return response()->json([], 201);
    }
}
