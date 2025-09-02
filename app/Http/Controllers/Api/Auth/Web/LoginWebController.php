<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Web;

use App\Actions\Auth\LoginWebAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Web\LoginWebRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

final class LoginWebController extends Controller
{
    public function __construct(private readonly LoginWebAction $login) {}

    public function store(LoginWebRequest $request): JsonResponse
    {
        try {
            $user = $this->login->execute(
                (string) $request->input('email'),
                (string) $request->input('password'),
                $request->remember(),
            );

            return response()->json([
                'message' => 'Logged in.',
                'user' => [
                    'id' => (string) $user->getKey(),
                    'email' => $user->email,
                ],
            ]);
        } catch (RuntimeException $e) {
            $status = (int) $e->getCode();
            return response()->json(['message' => $e->getMessage()], $status > 0 ? $status : 400);
        }
    }
}
