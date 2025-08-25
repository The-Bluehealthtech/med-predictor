@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                Create License Template
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Define a new license template with custom requirements and settings
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('club-management.licenses.templates') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Templates
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form action="{{ route('club-management.licenses.templates.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Template Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Template Code *</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="e.g., senior_player, youth_player">
                            <p class="mt-1 text-sm text-gray-500">Unique identifier for the template</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Describe the purpose and requirements of this template">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- License Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">License Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="validity_period_months" class="block text-sm font-medium text-gray-700">Validity Period (Months) *</label>
                            <input type="number" name="validity_period_months" id="validity_period_months" value="{{ old('validity_period_months', 12) }}" min="1" max="60" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('validity_period_months')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="fee" class="block text-sm font-medium text-gray-700">License Fee *</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="fee" id="fee" value="{{ old('fee', 0) }}" min="0" step="0.01" required
                                    class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            @error('fee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Requirements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="requires_medical_check" id="requires_medical_check" value="1" {{ old('requires_medical_check', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="requires_medical_check" class="ml-2 block text-sm text-gray-900">
                                Requires Medical Check
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="requires_documents" id="requires_documents" value="1" {{ old('requires_documents', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="requires_documents" class="ml-2 block text-sm text-gray-900">
                                Requires Documents
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Required Fields -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Required Fields</h3>
                    <p class="text-sm text-gray-600 mb-4">Select the fields that are mandatory for this license type:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $availableFields = [
                                'player_name' => 'Player Name',
                                'date_of_birth' => 'Date of Birth',
                                'nationality' => 'Nationality',
                                'position' => 'Position',
                                'contract_start_date' => 'Contract Start Date',
                                'contract_end_date' => 'Contract End Date',
                                'salary' => 'Salary',
                                'medical_check_date' => 'Medical Check Date',
                                'emergency_contact' => 'Emergency Contact',
                                'agent_details' => 'Agent Details',
                                'previous_club' => 'Previous Club',
                                'transfer_fee' => 'Transfer Fee',
                                'performance_bonus' => 'Performance Bonus',
                                'image_rights' => 'Image Rights',
                                'sponsorship_deals' => 'Sponsorship Deals'
                            ];
                        @endphp
                        
                        @foreach($availableFields as $field => $label)
                        <div class="flex items-center">
                            <input type="checkbox" name="required_fields[]" id="required_{{ $field }}" value="{{ $field }}"
                                {{ in_array($field, old('required_fields', ['player_name', 'date_of_birth', 'nationality', 'position'])) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="required_{{ $field }}" class="ml-2 block text-sm text-gray-900">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('required_fields')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Optional Fields -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Optional Fields</h3>
                    <p class="text-sm text-gray-600 mb-4">Select additional fields that can be filled optionally:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($availableFields as $field => $label)
                        <div class="flex items-center">
                            <input type="checkbox" name="optional_fields[]" id="optional_{{ $field }}" value="{{ $field }}"
                                {{ in_array($field, old('optional_fields', [])) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="optional_{{ $field }}" class="ml-2 block text-sm text-gray-900">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('optional_fields')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Requirements -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Required Documents</h3>
                    <p class="text-sm text-gray-600 mb-4">Select the documents that must be uploaded for this license type:</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $availableDocuments = [
                                'passport' => 'Passport',
                                'national_id' => 'National ID',
                                'birth_certificate' => 'Birth Certificate',
                                'medical_certificate' => 'Medical Certificate',
                                'contract' => 'Player Contract',
                                'transfer_certificate' => 'Transfer Certificate',
                                'agent_contract' => 'Agent Contract',
                                'sponsorship_agreement' => 'Sponsorship Agreement',
                                'image_rights_agreement' => 'Image Rights Agreement',
                                'disciplinary_record' => 'Disciplinary Record',
                                'performance_reports' => 'Performance Reports',
                                'financial_disclosure' => 'Financial Disclosure'
                            ];
                        @endphp
                        
                        @foreach($availableDocuments as $doc => $label)
                        <div class="flex items-center">
                            <input type="checkbox" name="document_requirements[]" id="doc_{{ $doc }}" value="{{ $doc }}"
                                {{ in_array($doc, old('document_requirements', ['passport', 'medical_certificate', 'contract'])) ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="doc_{{ $doc }}" class="ml-2 block text-sm text-gray-900">
                                {{ $label }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('document_requirements')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Validation Rules -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Validation Rules</h3>
                    <p class="text-sm text-gray-600 mb-4">Define custom validation rules for specific fields (optional):</p>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="validation_age_min" class="block text-sm font-medium text-gray-700">Minimum Age</label>
                                <input type="number" name="validation_rules[age_min]" id="validation_age_min" value="{{ old('validation_rules.age_min', 16) }}" min="0" max="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label for="validation_age_max" class="block text-sm font-medium text-gray-700">Maximum Age</label>
                                <input type="number" name="validation_rules[age_max]" id="validation_age_max" value="{{ old('validation_rules.age_max', 40) }}" min="0" max="100"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label for="validation_contract_min_months" class="block text-sm font-medium text-gray-700">Min Contract (Months)</label>
                                <input type="number" name="validation_rules[contract_min_months]" id="validation_contract_min_months" value="{{ old('validation_rules.contract_min_months', 6) }}" min="1" max="60"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('club-management.licenses.templates') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate code from name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    
    nameInput.addEventListener('input', function() {
        if (!codeInput.value) {
            const code = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
            codeInput.value = code;
        }
    });
    
    // Toggle document requirements based on checkbox
    const requiresDocumentsCheckbox = document.getElementById('requires_documents');
    const documentCheckboxes = document.querySelectorAll('input[name="document_requirements[]"]');
    
    requiresDocumentsCheckbox.addEventListener('change', function() {
        documentCheckboxes.forEach(checkbox => {
            checkbox.disabled = !this.checked;
            if (!this.checked) {
                checkbox.checked = false;
            }
        });
    });
    
    // Initialize state
    documentCheckboxes.forEach(checkbox => {
        checkbox.disabled = !requiresDocumentsCheckbox.checked;
    });
});
</script>
@endsection 