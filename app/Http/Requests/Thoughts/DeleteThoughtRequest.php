<?php
declare(strict_types=1);
namespace App\Http\Requests\Thoughts;

use App\Models\Thought;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DeleteThoughtRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();

        /** @var Thought $thought */
        $thought = $this->route('thought');
        return $thought && $user && $thought->user_id === $user->id;
    }

    public function rules(): array
    {
        return [];
    }
}
