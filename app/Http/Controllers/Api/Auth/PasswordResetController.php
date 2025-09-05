<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Auth\ResetForgotPasswordContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;

final class PasswordResetController extends Controller
{
    public function __construct(private readonly ResetForgotPasswordContract $reset) {}

    public function store(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $result = $this->reset->execute(
                (string) $data['email'],
                (string) $data['password'],
                (string) ($data['password_confirmation'] ?? ''),
                (string) $data['token'],
            );

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            $status = (int) $e->getCode();
            return response()->json(['message' => $e->getMessage()], $status > 0 ? $status : 400);
        }
    }
}
