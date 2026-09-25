<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FederationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('manage_federations');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $federationId = $this->route('federation')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('federations')->ignore($federationId)
            ],
            'short_name' => [
                'required',
                'string',
                'max:10',
                Rule::unique('federations')->ignore($federationId)
            ],
            'country' => 'required|string|max:100',
            'region' => 'required|string|in:Europe,Asia,Africa,North America,South America,Oceania',
            'fifa_code' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
                Rule::unique('federations')->ignore($federationId)
            ],
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive,suspended',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The federation name is required.',
            'name.unique' => 'A federation with this name already exists.',
            'short_name.required' => 'The short name is required.',
            'short_name.unique' => 'A federation with this short name already exists.',
            'fifa_code.required' => 'The FIFA code is required.',
            'fifa_code.size' => 'The FIFA code must be exactly 3 characters.',
            'fifa_code.regex' => 'The FIFA code must contain only uppercase letters.',
            'fifa_code.unique' => 'A federation with this FIFA code already exists.',
            'region.in' => 'Please select a valid region.',
            'status.in' => 'Please select a valid status.',
            'website.url' => 'Please enter a valid website URL.',
            'email.email' => 'Please enter a valid email address.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'federation name',
            'short_name' => 'short name',
            'fifa_code' => 'FIFA code',
        ];
    }
}
