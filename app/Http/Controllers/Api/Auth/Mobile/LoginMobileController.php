<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Mobile;

use App\Actions\Auth\LoginMobileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Mobile\LoginMobileRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

final class LoginMobileController extends Controller
{
    public function __construct(private readonly LoginMobileAction $login) {}

    public function store(LoginMobileRequest $request): JsonResponse
    {
        try {
            $result = $this->login->execute(
                (string) $request->input('email'),
                (string) $request->input('password'),
                (string) $request->input('device_name'),
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            $status = (int) $e->getCode();
            if ($status <= 0) {
                $status = 400;
            }

            return response()->json(
                ['message' => $e->getMessage()],
                $status
            );
        }
    }
}
