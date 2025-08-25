@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                    <p class="mt-2 text-gray-600">Update user information and FIFA Connect settings</p>
                </div>
                <a href="{{ route('user-management.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('user-management.update', $user) }}" class="p-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                                   placeholder="Enter full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror"
                                   placeholder="Leave blank to keep current password">
                            <p class="mt-1 text-sm text-gray-500">Leave blank to keep the current password</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Confirm new password">
                        </div>
                    </div>
                </div>

                <!-- Role and Entity Selection -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Role & Entity Assignment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                            <select name="role" id="role" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('role') border-red-300 @enderror">
                                <option value="">Select a role</option>
                                @foreach($roles as $role => $display)
                                <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                    {{ $display }}
                                </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="entity-type-container" class="{{ in_array($user->role, ['club_admin', 'club_manager', 'club_medical', 'association_admin', 'association_registrar', 'association_medical']) ? '' : 'hidden' }}">
                            <label for="entity_type" class="block text-sm font-medium text-gray-700 mb-1">Entity Type *</label>
                            <select name="entity_type" id="entity_type"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('entity_type') border-red-300 @enderror">
                                <option value="">Select entity type</option>
                                <option value="club" {{ old('entity_type', $user->entity_type) == 'club' ? 'selected' : '' }}>Club</option>
                                <option value="association" {{ old('entity_type', $user->entity_type) == 'association' ? 'selected' : '' }}>Football Association</option>
                            </select>
                            @error('entity_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="entity-id-container" class="{{ $user->entity_id ? '' : 'hidden' }}">
                            <label for="entity_id" class="block text-sm font-medium text-gray-700 mb-1">Entity *</label>
                            <select name="entity_id" id="entity_id"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('entity_id') border-red-300 @enderror">
                                <option value="">Select entity</option>
                            </select>
                            @error('entity_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- FIFA Connect Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">FIFA Connect Integration</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Current FIFA Connect ID</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <code class="bg-blue-100 px-2 py-1 rounded text-xs">{{ $user->fifa_connect_id }}</code>
                                    <p class="mt-2">This ID will be regenerated if the role or entity changes to maintain FIFA Connect compliance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">Current Permissions</h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    @if($user->permissions)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($user->permissions as $permission)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ str_replace('_', ' ', $permission) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p>No specific permissions assigned</p>
                                    @endif
                                    <p class="mt-2">Permissions will be automatically updated based on the selected role.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <div class="text-sm font-medium text-gray-500">Login Count</div>
                            <div class="text-2xl font-bold text-gray-900">{{ $user->login_count }}</div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <div class="text-sm font-medium text-gray-500">Last Login</div>
                            <div class="text-sm text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                            <div class="text-sm font-medium text-gray-500">Member Since</div>
                            <div class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('user-management.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const entityTypeContainer = document.getElementById('entity-type-container');
    const entityTypeSelect = document.getElementById('entity_type');
    const entityIdContainer = document.getElementById('entity-id-container');
    const entityIdSelect = document.getElementById('entity_id');

    // Store entity data
    const entities = {
        club: @json($clubs),
        association: @json($associations)
    };

    // Role change handler
    roleSelect.addEventListener('change', function() {
        const role = this.value;
        const requiresEntity = ['club_admin', 'club_manager', 'club_medical', 'association_admin', 'association_registrar', 'association_medical'].includes(role);
        
        if (requiresEntity) {
            entityTypeContainer.classList.remove('hidden');
            entityTypeSelect.required = true;
        } else {
            entityTypeContainer.classList.add('hidden');
            entityTypeSelect.required = false;
            entityIdContainer.classList.add('hidden');
            entityIdSelect.required = false;
            entityIdSelect.innerHTML = '<option value="">Select entity</option>';
        }
    });

    // Entity type change handler
    entityTypeSelect.addEventListener('change', function() {
        const entityType = this.value;
        
        if (entityType && entities[entityType]) {
            entityIdContainer.classList.remove('hidden');
            entityIdSelect.required = true;
            
            // Populate entity options
            entityIdSelect.innerHTML = '<option value="">Select entity</option>';
            entities[entityType].forEach(entity => {
                const option = document.createElement('option');
                option.value = entity.id;
                option.textContent = entity.name;
                if (entity.id == {{ $user->entity_id ?? 'null' }}) {
                    option.selected = true;
                }
                entityIdSelect.appendChild(option);
            });
        } else {
            entityIdContainer.classList.add('hidden');
            entityIdSelect.required = false;
            entityIdSelect.innerHTML = '<option value="">Select entity</option>';
        }
    });

    // Trigger initial state
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
    if (entityTypeSelect.value) {
        entityTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection 