<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\MustBeUnverified;
use Illuminate\Foundation\Http\FormRequest;

final class ResendVerificationRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'exists:users,email',
                $this->user ? new MustBeUnverified($this->user) : 'prohibited',
            ],
        ];
    }

    public function targetUser(): ?User
    {
        return $this->user;
    }
}
