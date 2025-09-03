<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Web;

use App\Contracts\Auth\LoginWebContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Web\LoginWebRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

final class LoginWebController extends Controller
{
    public function __construct(private readonly LoginWebContract $login) {}

    public function store(LoginWebRequest $request): JsonResponse
    {
        try {
            $user = $this->login->execute(
                $request->string('email')->toString(),
                $request->string('password')->toString(),
                $request->remember(),
            );

            return response()->json([
                'message' => 'Logged in.',
                'user' => [
                    'id'         => (string) $user->getKey(),
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'email'      => $user->email,
                ],
            ], 200);
        } catch (RuntimeException $e) {
            $status = (int) $e->getCode();
            return response()->json(['message' => $e->getMessage()], $status > 0 ? $status : 400);
        }
    }
}
