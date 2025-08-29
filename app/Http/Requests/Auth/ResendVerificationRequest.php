<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ResendVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $user = $this->targetUser();

            if (! $user) {
                $v->errors()->add('id', 'User not found.');
                return;
            }

            if ($user->hasVerifiedEmail()) {
                $v->errors()->add('id', 'Email already verified.');
            }
        });
    }

    public function targetUser(): ?User
    {
        return User::find($this->input('id'));
    }
}
