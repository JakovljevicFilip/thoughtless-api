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
            'email'       => ['required', 'email:rfc,dns'],
            'password'    => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }
}
