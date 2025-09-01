<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Web;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

final class LoginWebRequest extends FormRequest
{
    private ?User $authenticatedUser = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }

            $email = (string) $this->input('email', '');
            $password = (string) $this->input('password', '');

            /** @var User|null $user */
            $user = User::where('email', $email)->first();

            if (! $user || ! Auth::guard('web')->validate(['email' => $email, 'password' => $password])) {
                throw new HttpResponseException(
                    response()->json(['message' => 'Invalid credentials.'], 401)
                );
            }

            if (! $user->hasVerifiedEmail()) {
                throw new HttpResponseException(
                    response()->json(['message' => 'Please verify your email before continuing.'], 403)
                );
            }

            $this->authenticatedUser = $user;
        });
    }

    public function getAuthenticatedUser(): ?User
    {
        return $this->authenticatedUser;
    }
}
