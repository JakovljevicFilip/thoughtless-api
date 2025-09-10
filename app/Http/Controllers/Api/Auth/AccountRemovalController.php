<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Auth\ScheduleAccountRemovalContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AccountRemovalRequest;
use Illuminate\Http\JsonResponse;

final class AccountRemovalController extends Controller
{
    public function __construct(
        private readonly ScheduleAccountRemovalContract $schedule,
    ) {}
    public function store(AccountRemovalRequest $request): JsonResponse
    {
        $this->schedule->execute($request->user(), (string) $request->validated('password'));
        return response()->json(['message' => 'Account deletion scheduled.']);
    }
}
