<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\ValidEmailVerificationToken;
use Illuminate\Foundation\Http\FormRequest;

final class VerifyEmailRequest extends FormRequest
{
    private ?User $user = null;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->user = User::where('email', $this->input('email'))->first();

        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => [
                'required',
                'string',
                $this->user ? new ValidEmailVerificationToken($this->user) : 'prohibited',
            ],
        ];
    }

    public function userForVerification(): ?User
    {
        return $this->user;
    }
}
