@extends('layouts.app')

@section('title', 'Create New Transfer')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Transfer</h1>
        <a href="{{ route('transfers.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Transfers
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form id="transferForm" method="POST" action="{{ route('transfers.store') }}" class="space-y-6">
            @csrf
            
            <!-- Player Selection -->
            <div>
                <label for="player_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Player <span class="text-red-500">*</span>
                </label>
                <select name="player_id" id="player_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('player_id') border-red-500 @enderror">
                    <option value="">Select a player</option>
                    @foreach($players as $player)
                        <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                            {{ $player->first_name }} {{ $player->last_name }} 
                            @if($player->club)
                                ({{ $player->club->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('player_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Clubs Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="club_origin_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Origin Club <span class="text-red-500">*</span>
                    </label>
                    <select name="club_origin_id" id="club_origin_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('club_origin_id') border-red-500 @enderror">
                        <option value="">Select origin club</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ old('club_origin_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('club_origin_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="club_destination_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Destination Club <span class="text-red-500">*</span>
                    </label>
                    <select name="club_destination_id" id="club_destination_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('club_destination_id') border-red-500 @enderror">
                        <option value="">Select destination club</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ old('club_destination_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('club_destination_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Transfer Type and Dates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="transfer_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Transfer Type <span class="text-red-500">*</span>
                    </label>
                    <select name="transfer_type" id="transfer_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('transfer_type') border-red-500 @enderror">
                        <option value="">Select transfer type</option>
                        <option value="permanent" {{ old('transfer_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="loan" {{ old('transfer_type') == 'loan' ? 'selected' : '' }}>Loan</option>
                        <option value="free_agent" {{ old('transfer_type') == 'free_agent' ? 'selected' : '' }}>Free Agent</option>
                    </select>
                    @error('transfer_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transfer_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Transfer Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="transfer_date" id="transfer_date" required 
                           value="{{ old('transfer_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('transfer_date') border-red-500 @enderror">
                    @error('transfer_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Contract Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="contract_start_date" id="contract_start_date" required 
                           value="{{ old('contract_start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_start_date') border-red-500 @enderror">
                    @error('contract_start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contract End Date and Transfer Fee -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Contract End Date
                    </label>
                    <input type="date" name="contract_end_date" id="contract_end_date"
                           value="{{ old('contract_end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_end_date') border-red-500 @enderror">
                    @error('contract_end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transfer_fee" class="block text-sm font-medium text-gray-700 mb-2">
                        Transfer Fee
                    </label>
                    <input type="number" name="transfer_fee" id="transfer_fee" step="0.01" min="0"
                           value="{{ old('transfer_fee') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('transfer_fee') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('transfer_fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        Currency <span class="text-red-500">*</span>
                    </label>
                    <select name="currency" id="currency" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('currency') border-red-500 @enderror">
                        <option value="">Select currency</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Options -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_minor_transfer" id="is_minor_transfer" value="1"
                           {{ old('is_minor_transfer') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_minor_transfer" class="ml-2 block text-sm text-gray-900">
                        Minor Transfer (Under 18)
                    </label>
                </div>
            </div>

            <!-- Special Conditions -->
            <div>
                <label for="special_conditions" class="block text-sm font-medium text-gray-700 mb-2">
                    Special Conditions
                </label>
                <textarea name="special_conditions" id="special_conditions" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('special_conditions') border-red-500 @enderror"
                          placeholder="Enter any special conditions for this transfer...">{{ old('special_conditions') }}</textarea>
                @error('special_conditions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                          placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('transfers.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    <i class="fas fa-save mr-2"></i>Create Transfer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('transferForm');
    const transferDate = document.getElementById('transfer_date');
    const contractStartDate = document.getElementById('contract_start_date');
    const contractEndDate = document.getElementById('contract_end_date');
    const clubOrigin = document.getElementById('club_origin_id');
    const clubDestination = document.getElementById('club_destination_id');

    // Auto-set contract start date to be after transfer date
    transferDate.addEventListener('change', function() {
        const transferDateValue = this.value;
        if (transferDateValue) {
            contractStartDate.min = transferDateValue;
            if (contractStartDate.value && contractStartDate.value < transferDateValue) {
                contractStartDate.value = '';
            }
        }
    });

    // Auto-set contract end date to be after start date
    contractStartDate.addEventListener('change', function() {
        const startDateValue = this.value;
        if (startDateValue) {
            contractEndDate.min = startDateValue;
            if (contractEndDate.value && contractEndDate.value < startDateValue) {
                contractEndDate.value = '';
            }
        }
    });

    // Validate that origin and destination clubs are different
    form.addEventListener('submit', function(e) {
        if (clubOrigin.value && clubDestination.value && clubOrigin.value === clubDestination.value) {
            e.preventDefault();
            alert('Origin and destination clubs must be different.');
            return false;
        }
    });

    // Handle form submission via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert(data.message);
                // Redirect to the transfer details page
                window.location.href = data.redirect_url;
            } else {
                // Show error message
                alert(data.message || 'An error occurred while creating the transfer.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the transfer.');
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
});
</script>
@endsection 