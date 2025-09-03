<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Mobile;

use App\Actions\Auth\LoginMobileAction;
use App\Contracts\Auth\LoginMobileContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Mobile\LoginMobileRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

final class LoginMobileController extends Controller
{
    public function __construct(private readonly LoginMobileContract $login) {}

    public function store(LoginMobileRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $result = $this->login->execute(
                $data['email'],
                $data['password'],
                $data['device_name'] ?? 'mobile',
            );

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            $status = (int) $e->getCode();
            return response()->json(['message' => $e->getMessage()], $status > 0 ? $status : 400);
        }
    }
}
