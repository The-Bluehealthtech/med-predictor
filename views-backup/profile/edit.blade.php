@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Profile</h1>
            
            <div class="space-y-6">
                <!-- User Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('profile.settings') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
