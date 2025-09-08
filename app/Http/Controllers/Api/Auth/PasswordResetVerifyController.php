<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Auth\VerifyForgotPasswordContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordVerifyRequest;
use Illuminate\Http\JsonResponse;

final class PasswordResetVerifyController extends Controller
{
    public function __construct(private readonly VerifyForgotPasswordContract $verify) {}

    public function store(ForgotPasswordVerifyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $result = $this->verify->execute(
                (string) $data['email'],
                (string) $data['token'],
            );

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            $status = (int) $e->getCode();
            return response()->json(['message' => $e->getMessage()], $status > 0 ? $status : 400);
        }
    }
}
