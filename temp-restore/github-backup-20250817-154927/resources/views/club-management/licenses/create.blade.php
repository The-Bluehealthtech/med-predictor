@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if(isset($club))
                            <!-- Club Logo -->
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                @if($club->hasLogo())
                                    <img src="{{ $club->getLogoUrl() }}" 
                                         alt="{{ $club->getLogoAlt() }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                        @endif
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                @if(isset($club))
                                    {{ $club->name }} - New Player License
                                @else
                                    New Player License Request
                                @endif
                            </h2>
                            <p class="text-gray-600 mt-1">
                                FIFA Connect Workflow - Complete player registration and licensing process
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Licenses
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FIFA Connect Workflow Steps -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-center space-x-8">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Pre-registration</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Submission</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Regulatory Check</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold">4</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Approval</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('club-management.licenses.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Step 1: Player Selection and Basic Information -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 1: Player Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">Select Player *</label>
                                <select name="player_id" id="player_id" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Choose a player...</option>
                                    @foreach($availablePlayers as $player)
                                        <option value="{{ $player->id }}" {{ $player && $player->id == $player->id ? 'selected' : '' }}>
                                            {{ $player->name }} ({{ $player->position }}) - {{ $player->nationality }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('player_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700 mb-2">FIFA Connect ID</label>
                                <input type="text" name="fifa_connect_id" id="fifa_connect_id" 
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       placeholder="Auto-generated if empty">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate FIFA Connect ID</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: License Type and Category -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 2: License Classification</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">License Type *</label>
                                <select name="license_type" id="license_type" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select license type...</option>
                                    @foreach($licenseTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('license_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="license_category" class="block text-sm font-medium text-gray-700 mb-2">Age Category *</label>
                                <select name="license_category" id="license_category" required class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select age category...</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('license_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Contract Information -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 3: Contract Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">Contract Start Date *</label>
                                <input type="date" name="contract_start_date" id="contract_start_date" required 
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('contract_start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">Contract End Date *</label>
                                <input type="date" name="contract_end_date" id="contract_end_date" required 
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @error('contract_end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="wage_agreement" class="block text-sm font-medium text-gray-700 mb-2">Wage Agreement (EUR)</label>
                                <input type="number" name="wage_agreement" id="wage_agreement" step="0.01" min="0"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label for="release_clause" class="block text-sm font-medium text-gray-700 mb-2">Release Clause (EUR)</label>
                                <input type="number" name="release_clause" id="release_clause" step="0.01" min="0"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       placeholder="0.00">
                            </div>

                            <div class="md:col-span-2">
                                <label for="bonus_structure" class="block text-sm font-medium text-gray-700 mb-2">Bonus Structure</label>
                                <textarea name="bonus_structure" id="bonus_structure" rows="3"
                                          class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          placeholder="Describe bonus structure, performance incentives, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Regulatory Compliance -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 4: Regulatory Compliance</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="medical_clearance" id="medical_clearance" value="1" required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="medical_clearance" class="ml-2 block text-sm text-gray-900">
                                    Medical Clearance Certificate *
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="fitness_certificate" id="fitness_certificate" value="1" required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="fitness_certificate" class="ml-2 block text-sm text-gray-900">
                                    Fitness Certificate *
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="international_clearance" id="international_clearance" value="1"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="international_clearance" class="ml-2 block text-sm text-gray-900">
                                    International Clearance (if applicable)
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="work_permit" id="work_permit" value="1"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="work_permit" class="ml-2 block text-sm text-gray-900">
                                    Work Permit (if applicable)
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label for="disciplinary_record" class="block text-sm font-medium text-gray-700 mb-2">Disciplinary Record</label>
                                <textarea name="disciplinary_record" id="disciplinary_record" rows="3"
                                          class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                          placeholder="Any disciplinary history, suspensions, etc."></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="visa_status" class="block text-sm font-medium text-gray-700 mb-2">Visa Status</label>
                                <input type="text" name="visa_status" id="visa_status"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                       placeholder="e.g., Valid work visa, EU citizen, etc.">
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Required Documents -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 5: Required Documents</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="identity_proof" class="block text-sm font-medium text-gray-700 mb-2">
                                    Proof of Identity (ID/Birth Certificate) *
                                </label>
                                <input type="file" name="identity_proof" id="identity_proof" required
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">PDF, JPG, JPEG, PNG (max 2MB)</p>
                                @error('identity_proof')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="photo_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Photo ID *
                                </label>
                                <input type="file" name="photo_id" id="photo_id" required
                                       accept=".jpg,.jpeg,.png"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">JPG, JPEG, PNG (max 2MB)</p>
                                @error('photo_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="residence_proof" class="block text-sm font-medium text-gray-700 mb-2">
                                    Proof of Residence
                                </label>
                                <input type="file" name="residence_proof" id="residence_proof"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">Required for minors</p>
                            </div>

                            <div>
                                <label for="parental_consent" class="block text-sm font-medium text-gray-700 mb-2">
                                    Parental Consent
                                </label>
                                <input type="file" name="parental_consent" id="parental_consent"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">Required for minors</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="player_contract" class="block text-sm font-medium text-gray-700 mb-2">
                                    Player Contract
                                </label>
                                <input type="file" name="player_contract" id="player_contract"
                                       accept=".pdf"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <p class="mt-1 text-xs text-gray-500">PDF only (max 2MB) - Required for professionals</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Additional Notes -->
                    <div class="pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Step 6: Additional Information</h3>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="Any additional information, special circumstances, or notes for the association..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('club-management.licenses.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Submit License Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate FIFA Connect ID if empty
    const fifaConnectIdField = document.getElementById('fifa_connect_id');
    if (fifaConnectIdField && !fifaConnectIdField.value) {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.random().toString(36).substring(2, 5).toUpperCase();
        fifaConnectIdField.value = `PL-${timestamp}-${random}`;
    }

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endsection 