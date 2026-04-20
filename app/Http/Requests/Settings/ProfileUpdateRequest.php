<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $gender = $this->input('gender');

        if (is_string($gender)) {
            $normalized = strtoupper(trim($gender));

            if ($normalized === 'F') {
                $this->merge(['gender' => 'female']);
            } elseif ($normalized === 'M') {
                $this->merge(['gender' => 'male']);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'favorite_activity' => ['nullable', 'string', 'max:255'],
        ];
    }
}
