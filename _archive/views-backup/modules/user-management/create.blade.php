@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
                    <p class="mt-2 text-gray-600">Create a new user with FIFA Connect compliance</p>
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
            <form method="POST" action="{{ route('user-management.store') }}" class="p-6">
                @csrf

                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                                   placeholder="Enter full name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input type="password" name="password" id="password" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror"
                                   placeholder="Enter password">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Confirm password">
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
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}
                                        data-permissions="{{ json_encode($role->permissions ?? []) }}"
                                        data-description="{{ $role->description }}">
                                    {{ $role->display_name }}
                                    @if($role->is_system_role)
                                        <span class="text-gray-500">(System)</span>
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Select a role to automatically set default permissions</p>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="association-container" class="hidden">
                            <label for="association_id" class="block text-sm font-medium text-gray-700 mb-1">Football Association</label>
                            <select name="association_id" id="association_id"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('association_id') border-red-300 @enderror">
                                <option value="">Select Football Association</option>
                                @foreach($associations as $association)
                                <option value="{{ $association->id }}" {{ old('association_id') == $association->id ? 'selected' : '' }}>
                                    {{ $association->name }} ({{ $association->country }})
                                </option>
                                @endforeach
                            </select>
                            @error('association_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="club-container" class="hidden">
                            <label for="club_id" class="block text-sm font-medium text-gray-700 mb-1">Club</label>
                            <select name="club_id" id="club_id"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('club_id') border-red-300 @enderror">
                                <option value="">Select Club</option>
                                @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }} ({{ $club->country }})
                                </option>
                                @endforeach
                            </select>
                            @error('club_id')
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
                                <h3 class="text-sm font-medium text-blue-800">FIFA Connect ID</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>A unique FIFA Connect ID will be automatically generated for this user based on their role and entity assignment. This ID ensures compliance with FIFA data standards and enables seamless integration with FIFA Connect services.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions & Authorizations</h3>
                    
                    <!-- Role-based Permissions Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Role-based Permissions</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Select the permissions that will be granted to this user. Default permissions are pre-selected based on the chosen role, but you can customize them as needed.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Categories -->
                    <div id="permissions-container" class="space-y-6">
                        <!-- Module Access Permissions -->
                        <div class="permission-category">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Module Access</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="player_registration_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Player Registration</div>
                                        <div class="text-xs text-gray-500">Access to player registration and management</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="competition_management_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Competition Management</div>
                                        <div class="text-xs text-gray-500">Manage competitions and fixtures</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="healthcare_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Healthcare</div>
                                        <div class="text-xs text-gray-500">Access to healthcare and medical records</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="match_sheet_management" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Match Sheet Management</div>
                                        <div class="text-xs text-gray-500">Create and manage match sheets</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="referee_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Referee Access</div>
                                        <div class="text-xs text-gray-500">Referee-specific functionality</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="health_record_management" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Health Record Management</div>
                                        <div class="text-xs text-gray-500">Create and manage health records</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Administrative Permissions -->
                        <div class="permission-category">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Administrative Access</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="user_management" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">User Management</div>
                                        <div class="text-xs text-gray-500">Create and manage users</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="system_administration" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">System Administration</div>
                                        <div class="text-xs text-gray-500">Full system administration access</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="back_office_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Back Office Access</div>
                                        <div class="text-xs text-gray-500">Access to back office features</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- FIFA Connect Permissions -->
                        <div class="permission-category">
                            <h4 class="text-md font-medium text-gray-900 mb-3">FIFA Connect Integration</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="fifa_connect_access" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">FIFA Connect Access</div>
                                        <div class="text-xs text-gray-500">Access FIFA Connect services</div>
                                    </div>
                                </label>

                                <label class="permission-item flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="fifa_data_sync" class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">FIFA Data Sync</div>
                                        <div class="text-xs text-gray-500">Synchronize data with FIFA</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Summary -->
                    <div id="permission-summary" class="mt-6 bg-gray-50 border border-gray-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Permissions Summary</h4>
                        <div id="selected-permissions-list" class="text-sm text-gray-600">
                            <p class="text-gray-500 italic">Select a role to see default permissions, or customize them above.</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create User
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
    const associationContainer = document.getElementById('association-container');
    const associationSelect = document.getElementById('association_id');
    const clubContainer = document.getElementById('club-container');
    const clubSelect = document.getElementById('club_id');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const selectedPermissionsList = document.getElementById('selected-permissions-list');

    // Role-based default permissions
    const rolePermissions = {
        'system_admin': [
            'player_registration_access',
            'competition_management_access',
            'healthcare_access',
            'system_administration',
            'user_management',
            'back_office_access',
            'fifa_connect_access',
            'fifa_data_sync'
        ],
        'club_admin': [
            'player_registration_access',
            'competition_management_access',
            'healthcare_access',
            'fifa_connect_access'
        ],
        'club_manager': [
            'player_registration_access',
            'competition_management_access',
            'healthcare_access',
            'fifa_connect_access'
        ],
        'club_medical': [
            'healthcare_access',
            'health_record_management'
        ],
        'association_admin': [
            'player_registration_access',
            'competition_management_access',
            'healthcare_access',
            'user_management',
            'fifa_connect_access',
            'fifa_data_sync'
        ],
        'association_registrar': [
            'player_registration_access',
            'competition_management_access',
            'fifa_connect_access'
        ],
        'association_medical': [
            'healthcare_access',
            'health_record_management'
        ],
        'referee': [
            'match_sheet_management',
            'referee_access'
        ],
        'assistant_referee': [
            'match_sheet_management'
        ],
        'fourth_official': [
            'match_sheet_management'
        ],
        'var_official': [
            'match_sheet_management'
        ],
        'match_commissioner': [
            'match_sheet_management',
            'competition_management_access'
        ],
        'match_official': [
            'match_sheet_management'
        ],
        'team_doctor': [
            'healthcare_access',
            'health_record_management'
        ],
        'physiotherapist': [
            'healthcare_access'
        ],
        'sports_scientist': [
            'healthcare_access'
        ]
    };

    // Permission display names
    const permissionNames = {
        'player_registration_access': 'Player Registration',
        'competition_management_access': 'Competition Management',
        'healthcare_access': 'Healthcare Access',
        'match_sheet_management': 'Match Sheet Management',
        'referee_access': 'Referee Access',
        'health_record_management': 'Health Record Management',
        'user_management': 'User Management',
        'system_administration': 'System Administration',
        'back_office_access': 'Back Office Access',
        'fifa_connect_access': 'FIFA Connect Access',
        'fifa_data_sync': 'FIFA Data Sync'
    };

    // Role change handler
    roleSelect.addEventListener('change', function() {
        const role = this.value;
        const selectedOption = this.options[this.selectedIndex];
        
        // Show/hide containers based on role
        if (['association_admin', 'association_registrar', 'association_medical'].includes(role)) {
            // Association roles - show association, optionally show club
            associationContainer.classList.remove('hidden');
            associationSelect.required = true;
            clubContainer.classList.remove('hidden');
            clubSelect.required = false;
        } else if (['club_admin', 'club_manager', 'club_medical'].includes(role)) {
            // Club roles - show club, optionally show association
            clubContainer.classList.remove('hidden');
            clubSelect.required = true;
            associationContainer.classList.remove('hidden');
            associationSelect.required = false;
        } else {
            // System admin or other roles - hide both
            associationContainer.classList.add('hidden');
            associationSelect.required = false;
            clubContainer.classList.add('hidden');
            clubSelect.required = false;
        }

        // Set default permissions for the selected role
        setDefaultPermissions(role, selectedOption);
    });

    // Set default permissions for a role
    function setDefaultPermissions(role, selectedOption) {
        // Clear all checkboxes first
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Get permissions from the selected role's data attribute
        let permissions = [];
        if (selectedOption && selectedOption.dataset.permissions) {
            try {
                permissions = JSON.parse(selectedOption.dataset.permissions);
            } catch (e) {
                console.error('Error parsing role permissions:', e);
            }
        }

        // If no permissions from data attribute, fall back to hardcoded defaults
        if (permissions.length === 0 && rolePermissions[role]) {
            permissions = rolePermissions[role];
        }

        // Check the permissions
        permissions.forEach(permission => {
            const checkbox = document.querySelector(`input[value="${permission}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });

        // Update permission summary
        updatePermissionSummary();
    }

    // Update permission summary display
    function updatePermissionSummary() {
        const selectedPermissions = Array.from(permissionCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => permissionNames[checkbox.value] || checkbox.value);

        if (selectedPermissions.length > 0) {
            const permissionList = selectedPermissions.map(permission => 
                `<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2 mb-1">${permission}</span>`
            ).join('');
            selectedPermissionsList.innerHTML = permissionList;
        } else {
            selectedPermissionsList.innerHTML = '<p class="text-gray-500 italic">No permissions selected</p>';
        }
    }

    // Listen for permission checkbox changes
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionSummary);
    });

    // Trigger initial state
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection 