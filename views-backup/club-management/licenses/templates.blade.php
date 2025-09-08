@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                                License Templates
                            </h2>
                            <p class="text-gray-600 mt-1">
                                Manage different license types and categories for players
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('club-management.licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Licenses
                        </a>
                        <a href="{{ route('club-management.licenses.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Template
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($templates as $template)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $template->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $template->code }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if($template->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($template->description)
                    <p class="text-sm text-gray-600 mb-4">{{ $template->description }}</p>
                    @endif

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Validity Period:</span>
                            <span class="font-medium text-gray-900">{{ $template->validity_period_months }} months</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">License Fee:</span>
                            <span class="font-medium text-gray-900">${{ number_format($template->fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Medical Check:</span>
                            <span class="font-medium {{ $template->requires_medical_check ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $template->requires_medical_check ? 'Required' : 'Not Required' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Documents:</span>
                            <span class="font-medium {{ $template->requires_documents ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $template->requires_documents ? 'Required' : 'Not Required' }}
                            </span>
                        </div>
                    </div>

                    @if($template->required_fields)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Required Fields:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($template->required_fields as $field)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $field)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($template->document_requirements)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Required Documents:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($template->document_requirements as $doc)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst(str_replace('_', ' ', $doc)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            {{ $template->licenses->count() }} licenses using this template
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('club-management.licenses.templates.edit', $template) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                            <button onclick="deleteTemplate({{ $template->id }})" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No license templates</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new license template.</p>
                    <div class="mt-6">
                        <a href="{{ route('club-management.licenses.templates.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Template
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Quick Actions -->
        @if($templates->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Create License from Template</h4>
                        <p class="text-sm text-gray-600 mb-3">Select a template to create a new license with predefined settings.</p>
                        <select id="template-select" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a template...</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <button onclick="createLicenseFromTemplate()" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create License
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Template Statistics</h4>
                        <div class="space-y-2">
                            @foreach($templates->take(3) as $template)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $template->name }}:</span>
                                <span class="font-medium text-gray-900">{{ $template->licenses->count() }} licenses</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Export Templates</h4>
                        <p class="text-sm text-gray-600 mb-3">Export all templates for backup or sharing.</p>
                        <button onclick="exportTemplates()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        // Here you would typically make an AJAX call to delete the template
        // For now, we'll show an alert
        alert('Template deletion functionality would be implemented here.');
    }
}

function createLicenseFromTemplate() {
    const templateId = document.getElementById('template-select').value;
    if (!templateId) {
        alert('Please select a template first.');
        return;
    }
    
    // Redirect to license creation with template pre-selected
    window.location.href = `{{ route('club-management.licenses.create') }}?template=${templateId}`;
}

function exportTemplates() {
    // Here you would typically make an AJAX call to export templates
    alert('Template export functionality would be implemented here.');
}
</script>
@endsection 