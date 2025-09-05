<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:' . User::MAX_FIRST_NAME_LENGTH],
            'last_name'  => ['required', 'string', 'max:' . User::MAX_LAST_NAME_LENGTH],
            'email' => ['required', 'email:rfc,dns', 'max:' . User::MAX_EMAIL_LENGTH, Rule::unique('users', 'email')],
            'password'   => ['required', 'string', new StrongPassword()],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }
}
