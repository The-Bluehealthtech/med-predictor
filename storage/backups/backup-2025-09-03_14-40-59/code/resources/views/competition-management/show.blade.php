@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('Competition Details') }}</h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('competitions.edit', $competition->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('competitions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de base -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Basic Information') }}</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('ID') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $competition->id }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $name = $competition->name ?? 'N/A';
                                    if (is_array($name) || is_object($name)) {
                                        $name = json_encode($name);
                                    }
                                @endphp
                                {{ $name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Format') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $format = $competition->format ?? 'N/A';
                                    if (is_array($format) || is_object($format)) {
                                        $format = json_encode($format);
                                    }
                                @endphp
                                {{ $format }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Type') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $type = $competition->type ?? 'N/A';
                                    if (is_array($type) || is_object($type)) {
                                        $type = json_encode($type);
                                    }
                                @endphp
                                {{ $type }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $status = $competition->status ?? 'N/A';
                                    if (is_array($status) || is_object($status)) {
                                        $status = json_encode($status);
                                    }
                                @endphp
                                {{ ucfirst($status) }}
                            </p>
                        </div>
                    </div>

                    <!-- Informations FIFA Connect ID -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('FIFA Connect ID Information') }}</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('FIFA Connect ID (Direct)') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $fifaConnectId = $competition->fifa_connect_id ?? 'N/A';
                                    if (is_array($fifaConnectId) || is_object($fifaConnectId)) {
                                        $fifaConnectId = json_encode($fifaConnectId);
                                    }
                                @endphp
                                {{ $fifaConnectId }}
                            </p>
                        </div>

                        @if($competition->fifaConnectId)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('FIFA ID (Relation)') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $competition->fifaConnectId->fifa_id ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Entity Status') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $competition->fifaConnectId->entity_status ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Sync Status') }}</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $competition->fifaConnectId->sync_status_text ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Last Sync') }}</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $competition->fifaConnectId->last_sync ? $competition->fifaConnectId->last_sync->format('d/m/Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('FIFA Connect ID Status') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('No FIFA Connect ID relation found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="mt-8 space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Additional Information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Season') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $season = $competition->season ?? 'N/A';
                                    if (is_object($season) && method_exists($season, 'name')) {
                                        $season = $season->name;
                                    } elseif (is_array($season) || is_object($season)) {
                                        $season = json_encode($season);
                                    }
                                @endphp
                                {{ $season }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Association') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $association = $competition->association ?? 'N/A';
                                    if (is_object($association) && method_exists($association, 'name')) {
                                        $association = $association->name;
                                    } elseif (is_array($association) || is_object($association)) {
                                        $association = json_encode($association);
                                    }
                                @endphp
                                {{ $association }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Created') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $competition->created_at ? $competition->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 