@extends('layouts.app')

@section('title', 'Create Federation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('federations.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i> Back to Federations
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Create New Federation</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('federations.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Federation Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Federation Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., Fédération Française de Football">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Name -->
                    <div>
                        <label for="short_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Short Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('short_name') border-red-500 @enderror"
                               placeholder="e.g., FFF">
                        @error('short_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- FIFA Code -->
                    <div>
                        <label for="fifa_code" class="block text-sm font-medium text-gray-700 mb-2">
                            FIFA Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="fifa_code" id="fifa_code" value="{{ old('fifa_code') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fifa_code') border-red-500 @enderror"
                               placeholder="e.g., FRA" maxlength="3" style="text-transform: uppercase;">
                        @error('fifa_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="country" id="country" value="{{ old('country') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('country') border-red-500 @enderror"
                               placeholder="e.g., France">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Region -->
                    <div>
                        <label for="region" class="block text-sm font-medium text-gray-700 mb-2">
                            Region <span class="text-red-500">*</span>
                        </label>
                        <select name="region" id="region" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('region') border-red-500 @enderror">
                            <option value="">Select Region</option>
                            @foreach($regions as $key => $region)
                                <option value="{{ $key }}" {{ old('region') == $key ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                        @error('region')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            @foreach($statuses as $key => $status)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                            Website
                        </label>
                        <input type="url" name="website" id="website" value="{{ old('website') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('website') border-red-500 @enderror"
                               placeholder="https://www.fff.fr">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               placeholder="contact@fff.fr">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                               placeholder="+33 1 44 31 73 00">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea name="address" id="address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                                  placeholder="87 Boulevard de Grenelle, 75738 Paris">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('federations.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>Create Federation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase FIFA code
    const fifaCodeInput = document.getElementById('fifa_code');
    fifaCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection 