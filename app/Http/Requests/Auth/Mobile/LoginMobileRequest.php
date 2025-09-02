<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth\Mobile;

use Illuminate\Foundation\Http\FormRequest;

final class LoginMobileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function deviceName(): string
    {
        return (string) $this->input('device_name', 'mobile');
    }
}
