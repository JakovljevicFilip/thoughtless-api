<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AccountRemovalRequest;
use Illuminate\Http\JsonResponse;

final class AccountRemovalController extends Controller
{
    public function store(AccountRemovalRequest $request): JsonResponse
    {
        return response()->json([]);
    }
}
