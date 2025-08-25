@extends('layouts.app')

@section('title', 'Create New Contract')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Contract</h1>
        <a href="{{ route('contracts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Contracts
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('contracts.store') }}" class="space-y-6">
            @csrf
            
            <!-- Player and Club Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            </option>
                        @endforeach
                    </select>
                    @error('player_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="club_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Club <span class="text-red-500">*</span>
                    </label>
                    <select name="club_id" id="club_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('club_id') border-red-500 @enderror">
                        <option value="">Select a club</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('club_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contract Type and Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Contract Type <span class="text-red-500">*</span>
                    </label>
                    <select name="contract_type" id="contract_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_type') border-red-500 @enderror">
                        <option value="">Select contract type</option>
                        @foreach($contractTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('contract_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('contract_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Select status</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contract Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required 
                           value="{{ old('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required 
                           value="{{ old('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Salary Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="salary" class="block text-sm font-medium text-gray-700 mb-2">
                        Salary <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="salary" id="salary" required step="0.01" min="0"
                           value="{{ old('salary') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('salary') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('salary')
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
                        @foreach($currencies as $value => $label)
                            <option value="{{ $value }}" {{ old('currency') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bonus" class="block text-sm font-medium text-gray-700 mb-2">
                        Bonus
                    </label>
                    <input type="number" name="bonus" id="bonus" step="0.01" min="0"
                           value="{{ old('bonus') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bonus') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('bonus')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contract Terms -->
            <div>
                <label for="terms" class="block text-sm font-medium text-gray-700 mb-2">
                    Contract Terms
                </label>
                <textarea name="terms" id="terms" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('terms') border-red-500 @enderror"
                          placeholder="Enter contract terms and conditions...">{{ old('terms') }}</textarea>
                @error('terms')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Information -->
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
                <a href="{{ route('contracts.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    <i class="fas fa-save mr-2"></i>Create Contract
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-set end date to be after start date
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate) {
        endDateInput.min = startDate;
        if (endDateInput.value && endDateInput.value < startDate) {
            endDateInput.value = '';
        }
    }
});
</script>
@endsection 