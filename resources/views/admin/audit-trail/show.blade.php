@extends('layouts.app')

@section('title', 'Détails du Log - Audit Trail')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-orange-600 to-red-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📋</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Détails du Log #{{ $log->id }}
                                </h1>
                                <p class="text-sm text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.audit-trail.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à l'Audit Trail</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations générales -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations Générales</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID du Log</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date et Heure</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type d'événement</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center">
                                    {{ $log->event_type_icon }} {{ ucfirst($log->event_type) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Action</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center">
                                    {{ $log->action_icon }} {{ ucfirst($log->action) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sévérité</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $log->severity_color }}-100 text-{{ $log->severity_color }}-800">
                                    {{ ucfirst($log->severity) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Module</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->module ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Informations utilisateur -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Utilisateur</h3>
                    @if($log->user)
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $log->user->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $log->user->email ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->user->role ?? 'N/A')) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID Utilisateur</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $log->user_id }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-gray-500">Aucun utilisateur associé (action système)</p>
                    @endif
                </div>
            </div>

            <!-- Informations réseau -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations Réseau</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adresse IP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->ip_address ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Méthode HTTP</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->method ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">URL</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $log->url ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $log->user_agent ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Informations modèle -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informations Modèle</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de Modèle</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->model_type ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID du Modèle</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->model_id ?? 'N/A' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Nom du Modèle</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $log->model_name ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Description -->
            @if($log->description)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Description</h3>
                    <p class="text-sm text-gray-900">{{ $log->description }}</p>
                </div>
            </div>
            @endif

            <!-- Changements -->
            @if($log->changes && count($log->changes) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Changements</h3>
                    <div class="space-y-4">
                        @foreach($log->changes as $field => $value)
                        <div class="border-l-4 border-blue-400 pl-4">
                            <dt class="text-sm font-medium text-gray-500">{{ $field }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Valeurs anciennes -->
            @if($log->old_values && count($log->old_values) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Valeurs Anciennes</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <!-- Valeurs nouvelles -->
            @if($log->new_values && count($log->new_values) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Valeurs Nouvelles</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <!-- Métadonnées -->
            @if($log->metadata && count($log->metadata) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Métadonnées</h3>
                    <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
