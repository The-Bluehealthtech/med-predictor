@extends('layouts.app')

@section('title', 'Content Management - Administration')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-pink-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">📝</span>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Content Management
                                </h1>
                                <p class="text-sm text-gray-600">Gérer les articles, pages, médias et contenu du site</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('modules.administration.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à l'Administration</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📰</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Articles</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_articles'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📄</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Pages</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-blue-600">{{ $stats['total_pages'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">🎬</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Médias</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-purple-600">{{ $stats['total_media'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">📢</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Annonces</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-orange-600">{{ $stats['pending_reviews'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">❓</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">FAQ</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-indigo-600">{{ $stats['total_categories'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Publiés</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-green-600">{{ $stats['published_content'] }}</div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Types de contenu -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Types de Contenu</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($contentTypes as $type => $config)
                        <a href="{{ route('admin.content-management.' . $type) }}" 
                           class="block p-6 rounded-lg border-2 border-gray-200 hover:border-{{ $config['color'] }}-300 hover:bg-{{ $config['color'] }}-50 transition-all">
                            <div class="flex items-center mb-3">
                                <span class="text-3xl mr-4">{{ $config['icon'] }}</span>
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900">{{ $config['name'] }}</h4>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ $config['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions Rapides</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.content-management.create', ['type' => 'article']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        📰 Nouvel Article
                    </a>
                    <a href="{{ route('admin.content-management.create', ['type' => 'page']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        📄 Nouvelle Page
                    </a>
                    <a href="{{ route('admin.content-management.create', ['type' => 'announcement']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        📢 Nouvelle Annonce
                    </a>
                    <a href="{{ route('admin.content-management.create', ['type' => 'faq']) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        ❓ Nouvelle FAQ
                    </a>
                </div>
            </div>
        </div>

        <!-- Guide utilisateur -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-3xl mr-4">📚</span>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Guide Utilisateur</h3>
                            <p class="text-sm text-gray-600">Découvrez comment utiliser efficacement le Content Management avec des copies d'écran détaillées</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.content-management.user-guide') }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                        📖 Consulter le Guide
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
