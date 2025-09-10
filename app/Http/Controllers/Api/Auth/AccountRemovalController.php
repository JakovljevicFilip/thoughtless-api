<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountRemovalController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        return response()->json([]);
    }
}
