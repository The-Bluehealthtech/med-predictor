<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('manage_contracts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'player_id' => 'required|exists:players,id',
            'club_id' => 'required|exists:clubs,id',
            'contract_type' => 'required|string|in:professional,amateur,youth,loan',
            'status' => 'required|string|in:active,expired,terminated,suspended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'currency' => 'required|string|in:EUR,USD,GBP',
            'bonus_performance' => 'nullable|numeric|min:0',
            'bonus_appearance' => 'nullable|numeric|min:0',
            'bonus_goals' => 'nullable|numeric|min:0',
            'bonus_assists' => 'nullable|numeric|min:0',
            'release_clause' => 'nullable|numeric|min:0',
            'buyout_clause' => 'nullable|numeric|min:0',
            'contract_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('contracts')->ignore($this->route('contract')?->id)
            ],
            'fifa_contract_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('contracts')->ignore($this->route('contract')?->id)
            ],
            'agent_commission' => 'nullable|numeric|min:0|max:100',
            'agent_commission_amount' => 'nullable|numeric|min:0',
            'additional_terms' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
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
            'player_id.required' => 'Please select a player.',
            'player_id.exists' => 'The selected player does not exist.',
            'club_id.required' => 'Please select a club.',
            'club_id.exists' => 'The selected club does not exist.',
            'contract_type.required' => 'Please select a contract type.',
            'contract_type.in' => 'Please select a valid contract type.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Please select a valid status.',
            'start_date.required' => 'The start date is required.',
            'end_date.required' => 'The end date is required.',
            'end_date.after' => 'The end date must be after the start date.',
            'salary.required' => 'The salary is required.',
            'salary.numeric' => 'The salary must be a number.',
            'salary.min' => 'The salary cannot be negative.',
            'currency.required' => 'Please select a currency.',
            'currency.in' => 'Please select a valid currency.',
            'bonus_performance.numeric' => 'The performance bonus must be a number.',
            'bonus_performance.min' => 'The performance bonus cannot be negative.',
            'bonus_appearance.numeric' => 'The appearance bonus must be a number.',
            'bonus_appearance.min' => 'The appearance bonus cannot be negative.',
            'bonus_goals.numeric' => 'The goals bonus must be a number.',
            'bonus_goals.min' => 'The goals bonus cannot be negative.',
            'bonus_assists.numeric' => 'The assists bonus must be a number.',
            'bonus_assists.min' => 'The assists bonus cannot be negative.',
            'release_clause.numeric' => 'The release clause must be a number.',
            'release_clause.min' => 'The release clause cannot be negative.',
            'buyout_clause.numeric' => 'The buyout clause must be a number.',
            'buyout_clause.min' => 'The buyout clause cannot be negative.',
            'contract_number.unique' => 'A contract with this number already exists.',
            'fifa_contract_id.unique' => 'A contract with this FIFA ID already exists.',
            'agent_commission.numeric' => 'The agent commission must be a number.',
            'agent_commission.min' => 'The agent commission cannot be negative.',
            'agent_commission.max' => 'The agent commission cannot exceed 100%.',
            'agent_commission_amount.numeric' => 'The agent commission amount must be a number.',
            'agent_commission_amount.min' => 'The agent commission amount cannot be negative.',
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
            'player_id' => 'player',
            'club_id' => 'club',
            'contract_type' => 'contract type',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'contract_number' => 'contract number',
            'fifa_contract_id' => 'FIFA contract ID',
            'agent_commission' => 'agent commission',
            'agent_commission_amount' => 'agent commission amount',
        ];
    }
}
