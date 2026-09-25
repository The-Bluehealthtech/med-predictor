@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg mb-6">
            <div class="px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Settings & Preferences</h1>
                        <p class="text-orange-100 mt-2">Manage your referee account settings</p>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('referee.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-lg text-white hover:bg-opacity-30 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Account Settings</h2>
                
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('referee.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div>
                            <label for="fifa_connect_id" class="block text-sm font-medium text-gray-700">FIFA Connect ID</label>
                            <input type="text" name="fifa_connect_id" id="fifa_connect_id" value="{{ old('fifa_connect_id', $user->fifa_connect_id) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" readonly>
                            <p class="mt-1 text-sm text-gray-500">This is managed by the system administrator</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Preferences</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="preferences[email_notifications]" id="email_notifications" 
                                       value="1" {{ $user->preferences['email_notifications'] ?? false ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                                    Receive email notifications for match assignments
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="preferences[sms_notifications]" id="sms_notifications" 
                                       value="1" {{ $user->preferences['sms_notifications'] ?? false ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="sms_notifications" class="ml-2 block text-sm text-gray-900">
                                    Receive SMS notifications for urgent updates
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="preferences[auto_save]" id="auto_save" 
                                       value="1" {{ $user->preferences['auto_save'] ?? true ? 'checked' : '' }}
                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="auto_save" class="ml-2 block text-sm text-gray-900">
                                    Auto-save match sheet entries
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Information -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-medium text-gray-900">Account Created</h3>
                        <p class="text-gray-600">{{ $user->created_at->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Last Login</h3>
                        <p class="text-gray-600">{{ $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never' }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Role</h3>
                        <p class="text-gray-600">{{ ucfirst($user->role) }}</p>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Status</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 