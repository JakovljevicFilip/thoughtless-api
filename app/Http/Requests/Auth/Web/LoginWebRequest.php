<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Web;

use Illuminate\Foundation\Http\FormRequest;

final class LoginWebRequest extends FormRequest
{
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

    public function remember(): bool
    {
        return (bool) $this->input('remember', false);
    }
}
