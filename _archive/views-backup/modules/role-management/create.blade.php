@extends('layouts.app')

@section('title', 'Create New Role')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('role-management.index') }}" class="text-blue-600 hover:text-blue-900 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Role</h1>
                    <p class="text-gray-600 mt-2">Define a new role with specific permissions</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('role-management.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., senior_manager">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Unique identifier for the role (lowercase, underscores)</p>
                    </div>
                    
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Display Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror"
                               placeholder="e.g., Senior Manager">
                        @error('display_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Describe the role's purpose and responsibilities">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="fifa_connect_id_prefix" class="block text-sm font-medium text-gray-700 mb-2">
                        FIFA Connect ID Prefix
                    </label>
                    <input type="text" name="fifa_connect_id_prefix" id="fifa_connect_id_prefix" value="{{ old('fifa_connect_id_prefix') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fifa_connect_id_prefix') border-red-500 @enderror"
                           placeholder="e.g., FIFA_SENIOR_MGR">
                    @error('fifa_connect_id_prefix')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Prefix for FIFA Connect IDs assigned to users with this role</p>
                </div>
                
                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active Role</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1">Inactive roles cannot be assigned to new users</p>
                </div>
            </div>

            <!-- Permissions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Permissions</h2>
                
                <!-- Permission Categories -->
                <div class="space-y-6">
                    <!-- Module Access Permissions -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Module Access</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach([
                                'player_registration_access' => 'Player Registration Access',
                                'competition_management_access' => 'Competition Management Access',
                                'healthcare_access' => 'Healthcare Access',
                                'match_sheet_management' => 'Match Sheet Management',
                                'referee_access' => 'Referee Access',
                                'health_record_management' => 'Health Record Management'
                            ] as $permission => $label)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                           {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Administrative Permissions -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Administrative</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach([
                                'user_management' => 'User Management',
                                'system_administration' => 'System Administration',
                                'back_office_access' => 'Back Office Access'
                            ] as $permission => $label)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                           {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- FIFA Connect Permissions -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">FIFA Connect</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach([
                                'fifa_connect_access' => 'FIFA Connect Access',
                                'fifa_data_sync' => 'FIFA Data Sync'
                            ] as $permission => $label)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                           {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Quick Selection Buttons -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Selection</h4>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="selectAllPermissions()" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                            Select All
                        </button>
                        <button type="button" onclick="clearAllPermissions()" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                            Clear All
                        </button>
                        <button type="button" onclick="selectModuleAccess()" class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200">
                            Module Access Only
                        </button>
                        <button type="button" onclick="selectAdminPermissions()" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                            Admin Only
                        </button>
                    </div>
                </div>

                <!-- Permission Summary -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Permissions (<span id="permission-count">0</span>)</h4>
                    <div id="permission-summary" class="text-sm text-gray-600">
                        No permissions selected
                    </div>
                </div>
            </div>

            <!-- System Role Templates -->
            @if($systemRoles->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Based on System Role</h2>
                <p class="text-gray-600 mb-4">You can use an existing system role as a template for your new role:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($systemRoles as $systemRole)
                        <button type="button" onclick="loadRoleTemplate('{{ $systemRole->name }}')" 
                                class="text-left p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-300 transition-colors">
                            <div class="font-medium text-gray-900">{{ $systemRole->display_name }}</div>
                            <div class="text-sm text-gray-500 mt-1">{{ $systemRole->description }}</div>
                            <div class="text-xs text-gray-400 mt-2">{{ count($systemRole->permissions ?? []) }} permissions</div>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('role-management.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Permission management
function updatePermissionSummary() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
    const count = checkboxes.length;
    const summary = document.getElementById('permission-summary');
    const countElement = document.getElementById('permission-count');
    
    countElement.textContent = count;
    
    if (count === 0) {
        summary.textContent = 'No permissions selected';
    } else {
        const permissions = Array.from(checkboxes).map(cb => cb.value.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
        summary.textContent = permissions.join(', ');
    }
}

function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
    updatePermissionSummary();
}

function clearAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    updatePermissionSummary();
}

function selectModuleAccess() {
    clearAllPermissions();
    const modulePermissions = [
        'player_registration_access',
        'competition_management_access',
        'healthcare_access',
        'match_sheet_management',
        'referee_access',
        'health_record_management'
    ];
    modulePermissions.forEach(permission => {
        const checkbox = document.querySelector(`input[value="${permission}"]`);
        if (checkbox) checkbox.checked = true;
    });
    updatePermissionSummary();
}

function selectAdminPermissions() {
    clearAllPermissions();
    const adminPermissions = [
        'user_management',
        'system_administration',
        'back_office_access'
    ];
    adminPermissions.forEach(permission => {
        const checkbox = document.querySelector(`input[value="${permission}"]`);
        if (checkbox) checkbox.checked = true;
    });
    updatePermissionSummary();
}

function loadRoleTemplate(roleName) {
    // This would typically make an AJAX call to get the role's permissions
    // For now, we'll show a confirmation dialog
    if (confirm(`Load permissions from the "${roleName}" role? This will replace your current selections.`)) {
        // In a real implementation, you'd fetch the role data and populate the form
        console.log('Loading template for role:', roleName);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePermissionSummary();
    
    // Add event listeners to checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updatePermissionSummary);
    });
});
</script>
@endpush
@endsection 