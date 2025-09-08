<?php

namespace App\Http\Requests\Api\V1\Club;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClubRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'short_name' => ['sometimes', 'required', 'string', 'max:50'],
            'association_id' => ['sometimes', 'required', 'exists:associations,id'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'suspended'])],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Club name is required.',
            'short_name.required' => 'Short name is required.',
            'association_id.required' => 'Association is required.',
            'association_id.exists' => 'Selected association does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
            'founded_year.integer' => 'Founded year must be a number.',
            'founded_year.min' => 'Founded year must be after 1800.',
            'founded_year.max' => 'Founded year cannot be in the future.',
            'email.email' => 'Please provide a valid email address.',
            'website.url' => 'Please provide a valid website URL.',
            'logo_url.url' => 'Please provide a valid logo URL.',
        ];
    }
} 