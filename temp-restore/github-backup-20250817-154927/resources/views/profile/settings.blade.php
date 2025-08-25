@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Profile Settings</h1>
            
            <div class="space-y-6">
                <!-- Notification Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email Notifications</label>
                                <p class="text-sm text-gray-500">Receive notifications via email</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Medical Alerts</label>
                                <p class="text-sm text-gray-500">Receive medical alert notifications</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Performance Updates</label>
                                <p class="text-sm text-gray-500">Receive performance update notifications</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Privacy Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Privacy Settings</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Profile Visibility</label>
                                <p class="text-sm text-gray-500">Make profile visible to other users</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Data Sharing</label>
                                <p class="text-sm text-gray-500">Allow data sharing with FIFA Connect</p>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Security Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                            <p class="text-sm text-gray-500 mb-2">Add an extra layer of security to your account</p>
                            <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Enable 2FA
                            </button>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Change Password</label>
                            <p class="text-sm text-gray-500 mb-2">Update your account password</p>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Change Password
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 