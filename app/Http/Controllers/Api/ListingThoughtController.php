<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListingThoughtController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse([]);
    }
}
