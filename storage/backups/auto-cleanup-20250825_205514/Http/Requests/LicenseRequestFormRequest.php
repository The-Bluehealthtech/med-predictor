<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicenseRequestFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifier que l'utilisateur a le rôle approprié
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'license_agent', 'captain']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'player_id' => [
                'required',
                'integer',
                'exists:players,id',
                // Vérifier que le joueur appartient au club de l'utilisateur
                Rule::exists('players', 'id')->where(function ($query) {
                    $query->where('club_id', auth()->user()->club_id);
                })
            ],
            'license_type' => [
                'required',
                'string',
                'in:amateur,professional,international'
            ],
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048', // 2MB max
                'mimetypes:application/pdf,image/jpeg,image/png'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'requested_by' => [
                'required',
                'integer',
                'exists:users,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'player_id.required' => 'Le joueur est requis.',
            'player_id.exists' => 'Le joueur sélectionné n\'existe pas ou n\'appartient pas à votre club.',
            'license_type.required' => 'Le type de licence est requis.',
            'license_type.in' => 'Le type de licence doit être amateur, professionnel ou international.',
            'document.required' => 'Le document est requis.',
            'document.file' => 'Le document doit être un fichier.',
            'document.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'document.max' => 'Le document ne doit pas dépasser 2 Mo.',
            'document.mimetypes' => 'Le type de fichier n\'est pas autorisé.',
            'notes.max' => 'Les notes ne doivent pas dépasser 1000 caractères.',
            'requested_by.required' => 'L\'utilisateur qui fait la demande est requis.',
            'requested_by.exists' => 'L\'utilisateur qui fait la demande n\'existe pas.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'player_id' => 'joueur',
            'license_type' => 'type de licence',
            'document' => 'document',
            'notes' => 'notes',
            'requested_by' => 'demandeur'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ajouter automatiquement l'utilisateur connecté comme demandeur
        $this->merge([
            'requested_by' => auth()->id()
        ]);
    }
} 