<?php

namespace App\Http\Controllers\Api\Thoughts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thoughts\StoreThoughtRequest;
use App\Models\Thought;
use Illuminate\Http\JsonResponse;

class CreateThoughtController extends Controller
{
    public function store(StoreThoughtRequest $request): JsonResponse
    {
        $thought = Thought::create($request->validated());

        return response()->json($thought, 201);
    }
}
