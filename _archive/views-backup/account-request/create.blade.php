@extends('layouts.app')

@section('title', 'Demander un Compte - FIT Platform')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center">
                            <img src="{{ asset('images/fit-logo.png') }}" alt="FIT Logo" class="w-10 h-10 mr-3">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Demander un Compte
                                </h1>
                                <p class="text-sm text-gray-600">Football Intelligence & Tracking</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">← Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Demander un Accès à la Plateforme FIT
            </h2>
            <p class="text-lg text-gray-600 mb-6">
                Décrivez votre organisation et le type de football pour commencer avec la plateforme FIT
            </p>
            <div class="flex justify-center space-x-4">
                <div class="flex items-center text-sm text-gray-500">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Processus sécurisé
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    Validation par nos équipes
                </div>
            </div>
        </div>

        <!-- Account Request Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            @include('components.account-request-form-simple')
        </div>

        <!-- Information Section -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">ℹ️ Informations importantes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                <div>
                    <h4 class="font-medium mb-2">📋 Processus de validation</h4>
                    <ul class="space-y-1">
                        <li>• Vérification de l'organisation</li>
                        <li>• Validation des informations</li>
                        <li>• Création du compte sous 48h</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-2">🔒 Sécurité des données</h4>
                    <ul class="space-y-1">
                        <li>• Données chiffrées</li>
                        <li>• Conformité RGPD</li>
                        <li>• Accès sécurisé</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
