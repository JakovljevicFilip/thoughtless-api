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
        $this->user = User::findOrFail($this->input('id'));

        return [
            'id'    => ['required', 'uuid', 'exists:users,id'],
            'token' => ['required', 'string', new ValidEmailVerificationToken($this->user)],
        ];
    }

    public function userForVerification(): User
    {
        return $this->user;
    }
}
