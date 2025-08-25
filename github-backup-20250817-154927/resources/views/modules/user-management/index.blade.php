@extends('layouts.app')

@section('title', __('user_management.title'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('user_management.title') }}</h1>
                    <p class="mt-2 text-gray-600">{{ __('user_management.subtitle') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('user_management.back_to_dashboard') }}
                    </a>
                    <a href="{{ route('user-management.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('user_management.create_user') }}
                    </a>
                    <a href="{{ route('user-management.export') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('user_management.export') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('user-management.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('user_management.search') }}</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="{{ __('user_management.search_placeholder') }}">
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">{{ __('user_management.role') }}</label>
                            <select name="role" id="role" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">{{ __('user_management.all_roles') }}</option>
                                @foreach($roles as $role => $display)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $display }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('user_management.status') }}</label>
                            <select name="status" id="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">{{ __('user_management.all_statuses') }}</option>
                                @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Entity Type Filter -->
                        <div>
                            <label for="entity_type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('user_management.entity_type') }}</label>
                            <select name="entity_type" id="entity_type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">{{ __('user_management.all_types') }}</option>
                                @foreach($entityTypes as $type)
                                <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            {{ __('user_management.filter') }}
                        </button>
                        <a href="{{ route('user-management.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">{{ __('user_management.clear_filters') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="POST" action="{{ route('user-management.bulk-action') }}" id="bulk-action-form">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <select name="action" id="bulk-action" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">{{ __('user_management.bulk_actions') }}</option>
                                <option value="activate">{{ __('user_management.activate') }}</option>
                                <option value="deactivate">{{ __('user_management.deactivate') }}</option>
                                <option value="suspend">{{ __('user_management.suspend') }}</option>
                                <option value="delete">{{ __('user_management.delete') }}</option>
                            </select>
                            <button type="submit" id="bulk-action-btn" disabled
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">{{ __('user_management.apply') }}</button>
                        </div>
                        <div class="text-sm text-gray-600">
                            <span id="selected-count">0</span> {{ __('user_management.users_selected') }}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.user') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.role') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.entity') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.fifa_id') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.last_login') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.created') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('user_management.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->hasProfilePicture())
                                            <img src="{{ $user->getProfilePictureUrl() }}" 
                                                 alt="{{ $user->getProfilePictureAlt() }}" 
                                                 class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ $user->getInitials() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $user->isClubUser() ? 'bg-blue-100 text-blue-800' : 
                                       ($user->isAssociationUser() ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $user->getRoleDisplay() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->getEntityName() }}</div>
                                <div class="text-sm text-gray-500">{{ $user->getEntityTypeDisplay() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $user->fifa_connect_id }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($user->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('user-management.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="{{ __('user_management.view') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('user-management.edit', $user) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="{{ __('user_management.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if(!$user->isSystemAdmin() && $user->id !== auth()->id())
                                    <form method="POST" action="{{ route('user-management.destroy', $user) }}" class="inline" 
                                          onsubmit="return confirm('{{ __('user_management.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('user_management.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">{{ __('user_management.no_users_found') }}</p>
                                    <p class="text-gray-500">{{ __('user_management.try_adjusting_search') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionBtn = document.getElementById('bulk-action-btn');
    const selectedCount = document.getElementById('selected-count');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionButton();
    });

    // Individual checkbox functionality
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionButton();
            
            // Update select all checkbox
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const totalBoxes = userCheckboxes.length;
            selectAll.checked = checkedBoxes.length === totalBoxes;
            selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes;
        });
    });

    function updateBulkActionButton() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkAction = document.getElementById('bulk-action');
        
        selectedCount.textContent = checkedBoxes.length;
        bulkActionBtn.disabled = checkedBoxes.length === 0 || !bulkAction.value;
    }

    // Bulk action change
    document.getElementById('bulk-action').addEventListener('change', updateBulkActionButton);

    // Bulk action form submission
    document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkAction = document.getElementById('bulk-action').value;
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('{{ __('user_management.please_select_at_least_one_user') }}');
            return;
        }
        
        if (!bulkAction) {
            e.preventDefault();
            alert('{{ __('user_management.please_select_an_action') }}');
            return;
        }
        
        if (bulkAction === 'delete') {
            if (!confirm(`{{ __('user_management.confirm_delete_bulk') }}`)) {
                e.preventDefault();
                return;
            }
        }
    });
});
</script>
@endpush
@endsection 