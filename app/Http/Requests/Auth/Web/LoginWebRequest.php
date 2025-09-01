<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth\Web;

use Illuminate\Foundation\Http\FormRequest;

class LoginWebRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
