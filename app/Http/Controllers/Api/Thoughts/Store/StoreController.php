<?php

namespace App\Http\Controllers\Api\Thoughts\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function store(): JsonResponse
    {
        return response()->json([], 201);
    }
}
