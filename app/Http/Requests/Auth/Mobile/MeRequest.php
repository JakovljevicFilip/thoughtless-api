<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth\Mobile;

use Illuminate\Foundation\Http\FormRequest;

final class MeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // We expect auth:sanctum to have run, but be explicit:
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        // No input for this endpoint (GET /api/me)
        return [];
    }
}
