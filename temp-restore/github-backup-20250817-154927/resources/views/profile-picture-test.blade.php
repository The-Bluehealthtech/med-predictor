<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Picture Test - Med Predictor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">🖼️ Profile Picture Test</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Current User Profile Picture</h2>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if(auth()->user()->hasProfilePicture())
                            <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                 alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                 class="w-24 h-24 rounded-full object-cover border-4 border-blue-100">
                        @else
                            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center border-4 border-blue-100">
                                <span class="text-blue-600 font-bold text-3xl">{{ auth()->user()->getInitials() }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                        <p class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                        <p class="text-xs text-gray-400 mt-1">Profile Picture URL: {{ auth()->user()->getProfilePictureUrl() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">All Test Users Profile Pictures</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $testUsers = [
                            ['email' => 'john.doe@testfc.com', 'name' => 'John Doe'],
                            ['email' => 'admin@testfc.com', 'name' => 'Admin User'],
                            ['email' => 'manager@testfc.com', 'name' => 'Club Manager'],
                            ['email' => 'medical@testfc.com', 'name' => 'Medical Staff']
                        ];
                    @endphp
                    
                    @foreach($testUsers as $userData)
                        @php
                            $user = App\Models\User::where('email', $userData['email'])->first();
                        @endphp
                        @if($user)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($user->hasProfilePicture())
                                        <img src="{{ $user->getProfilePictureUrl() }}" 
                                             alt="{{ $user->getProfilePictureAlt() }}" 
                                             class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center border-2 border-gray-200">
                                            <span class="text-blue-600 font-bold text-lg">{{ $user->getInitials() }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $user->role) }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Debug Information</h2>
                <div class="space-y-2 text-sm">
                    <div><strong>Current User ID:</strong> {{ auth()->user()->id }}</div>
                    <div><strong>Profile Picture URL:</strong> {{ auth()->user()->profile_picture_url }}</div>
                    <div><strong>Has Profile Picture:</strong> {{ auth()->user()->hasProfilePicture() ? 'YES' : 'NO' }}</div>
                    <div><strong>Get Profile Picture URL:</strong> {{ auth()->user()->getProfilePictureUrl() }}</div>
                    <div><strong>Get Initials:</strong> {{ auth()->user()->getInitials() }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 