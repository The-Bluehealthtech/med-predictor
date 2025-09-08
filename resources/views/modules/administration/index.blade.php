@extends('layouts.app')

@section('title', 'Administration')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('modules.index') }}" class="ml-4 text-gray-400 hover:text-gray-500">
                                Modules
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-gray-500">Administration</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">⚙️ Administration</h1>
            <p class="mt-2 text-gray-600">Gestion administrative du système FIT</p>
        </div>

        <!-- Administration Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-blue-500">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <span class="text-2xl">👤</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Gestion des Utilisateurs</h3>
                    <p class="text-gray-600 mb-4">Gérer les comptes utilisateurs, rôles et permissions</p>
                    <a href="{{ route('public-user-management') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        Accéder →
                    </a>
                </div>
            </div>

            <!-- System Stats -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-cyan-500">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-cyan-100">
                            <span class="text-2xl">📊</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Statistiques Système</h3>
                    <p class="text-gray-600 mb-4">Consulter les statistiques et métriques du système</p>
                    <a href="{{ route('admin.system.stats') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-cyan-600 hover:bg-cyan-700 transition-colors">
                        Accéder →
                    </a>
                </div>
            </div>

            <!-- Account Requests -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-500">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                            <span class="text-2xl">📝</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Demandes de Comptes</h3>
                    <p class="text-gray-600 mb-4">Approuver ou rejeter les demandes de création de comptes</p>
                    <a href="{{ route('admin.account-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
                        Accéder →
                    </a>
                </div>
            </div>

                                <!-- RBAC Management -->
                    <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
                        <div class="p-6 text-center">
                            <div class="mb-4">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                    <span class="text-2xl">🔐</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Gestion RBAC</h3>
                            <p class="text-gray-600 mb-4">Gérer les rôles, permissions et contrôle d'accès</p>
                            <a href="{{ route('admin.rbac.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                Accéder →
                            </a>
                        </div>
                    </div>

            <!-- System Parameters -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-gray-500">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                            <span class="text-2xl">⚙️</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Paramètres Système</h3>
                    <p class="text-gray-600 mb-4">Configurer les paramètres et constantes du système</p>
                    <a href="{{ route('admin.system-settings.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 transition-colors">
                        Accéder →
                    </a>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-center">
                    <div class="mb-4">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                            <span class="text-2xl">📋</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Audit Trail</h3>
                    <p class="text-gray-600 mb-4">Consulter l'historique des actions et modifications</p>
                    <a href="{{ route('admin.audit-trail.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                        Accéder →
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

