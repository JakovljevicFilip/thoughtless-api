<?php
// app/Http/Requests/Auth/ForgotPasswordVerifyRequest.php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class ForgotPasswordVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'exists:users,email'],
            'token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'The selected email is invalid.',
        ];
    }
}
