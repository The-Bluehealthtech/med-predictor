@extends('back-office.layouts.app')

@section('title', 'Create License Type - Back Office')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create License Type</h1>
                <p class="text-gray-600 mt-2">Define a new license type with its requirements and approval process</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('back-office.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back to Dashboard
                </a>
                <a href="{{ route('back-office.license-types.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <form method="POST" action="{{ route('back-office.license-types.store') }}" class="p-6">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">License Type Name *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Professional Player License">
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">License Code *</label>
                            <input type="text" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., PROF">
                            @error('code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describe the license type and its purpose">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Validity and Fees -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Validity and Fees</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="validity_period_months" class="block text-sm font-medium text-gray-700 mb-2">Validity Period (months) *</label>
                            <input type="number" 
                                   id="validity_period_months" 
                                   name="validity_period_months" 
                                   value="{{ old('validity_period_months', 12) }}"
                                   min="1" 
                                   max="120"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('validity_period_months')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="fee_amount" class="block text-sm font-medium text-gray-700 mb-2">Fee Amount *</label>
                            <input type="number" 
                                   id="fee_amount" 
                                   name="fee_amount" 
                                   value="{{ old('fee_amount', 0) }}"
                                   min="0" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('fee_amount')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="fee_currency" class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                            <select id="fee_currency" 
                                    name="fee_currency"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}" {{ old('fee_currency', 'USD') === $currency ? 'selected' : '' }}>
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fee_currency')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Requirements -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Document Requirements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_medical_clearance" 
                                       value="1"
                                       {{ old('requires_medical_clearance') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Medical Clearance</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_fitness_certificate" 
                                       value="1"
                                       {{ old('requires_fitness_certificate') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Fitness Certificate</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_contract" 
                                       value="1"
                                       {{ old('requires_contract') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Contract</span>
                            </label>
                        </div>
                        
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_work_permit" 
                                       value="1"
                                       {{ old('requires_work_permit') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Work Permit</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_international_clearance" 
                                       value="1"
                                       {{ old('requires_international_clearance') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">International Clearance</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Approval Process -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Process</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_club_approval" 
                                       value="1"
                                       {{ old('requires_club_approval', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requires Club Approval</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="requires_association_approval" 
                                       value="1"
                                       {{ old('requires_association_approval', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Requires Association Approval</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="max_players_per_club" class="block text-sm font-medium text-gray-700 mb-2">Max Players per Club</label>
                            <input type="number" 
                                   id="max_players_per_club" 
                                   name="max_players_per_club" 
                                   value="{{ old('max_players_per_club') }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Leave empty for no limit">
                            @error('max_players_per_club')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="max_players_total" class="block text-sm font-medium text-gray-700 mb-2">Max Players Total</label>
                            <input type="number" 
                                   id="max_players_total" 
                                   name="max_players_total" 
                                   value="{{ old('max_players_total') }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Leave empty for no limit">
                            @error('max_players_total')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Age Restrictions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Age Restrictions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="min_age" class="block text-sm font-medium text-gray-700 mb-2">Minimum Age</label>
                            <input type="number" 
                                   id="min_age" 
                                   name="age_restrictions[min_age]" 
                                   value="{{ old('age_restrictions.min_age') }}"
                                   min="0" 
                                   max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Leave empty for no minimum">
                        </div>
                        
                        <div>
                            <label for="max_age" class="block text-sm font-medium text-gray-700 mb-2">Maximum Age</label>
                            <input type="number" 
                                   id="max_age" 
                                   name="age_restrictions[max_age]" 
                                   value="{{ old('age_restrictions.max_age') }}"
                                   min="0" 
                                   max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Leave empty for no maximum">
                        </div>
                    </div>
                </div>

                <!-- Position Restrictions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Position Restrictions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="allowed_positions" class="block text-sm font-medium text-gray-700 mb-2">Allowed Positions</label>
                            <select id="allowed_positions" 
                                    name="position_restrictions[allowed_positions][]" 
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    size="6">
                                @foreach($positions as $position => $label)
                                    <option value="{{ $position }}" 
                                            {{ in_array($position, old('position_restrictions.allowed_positions', [])) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple positions. Leave empty to allow all positions.</p>
                        </div>
                        
                        <div>
                            <label for="excluded_positions" class="block text-sm font-medium text-gray-700 mb-2">Excluded Positions</label>
                            <select id="excluded_positions" 
                                    name="position_restrictions[excluded_positions][]" 
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    size="6">
                                @foreach($positions as $position => $label)
                                    <option value="{{ $position }}" 
                                            {{ in_array($position, old('position_restrictions.excluded_positions', [])) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple positions.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('back-office.license-types.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                        Create License Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 