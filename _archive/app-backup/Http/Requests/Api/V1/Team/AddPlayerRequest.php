<?php

namespace App\Http\Requests\Api\V1\Team;

use Illuminate\Foundation\Http\FormRequest;

class AddPlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'association_manager', 'club_manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'player_id' => 'required|exists:players,id',
            'role' => 'nullable|in:starter,substitute,reserve,loan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'player_id.required' => 'Player is required',
            'player_id.exists' => 'Selected player does not exist',
            'role.in' => 'Role must be starter, substitute, reserve, or loan',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'player_id' => 'player',
            'role' => 'player role',
        ];
    }
} 