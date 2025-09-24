<?php

namespace App\Http\Requests\Thoughts;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'thoughts' => ['required', 'array', 'min:1'],
            'thoughts.*.id' => ['required', 'regex:/^\d+$/'],
            'thoughts.*.content' => ['required', 'string', 'min:1'],
            'thoughts.*.created_at' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
