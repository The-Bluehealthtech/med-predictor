<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIT — {{ $association->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/fit-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fit-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <x-page-header
            :title="$association->name"
            :subtitle="collect([$association->country, $association->confederation])->filter()->implode(' · ')"
            eyebrow="Association"
            :back-href="url('/associations-view')"
            back-label="Retour aux associations"
            :count="$association->clubs ? $association->clubs->count() : 0"
            count-label="club(s)"
        >
            @if(\App\Http\Controllers\OrganizationCardController::canEdit(auth()->user(), 'associations', $association))
                <x-slot:actions>
                    <a href="{{ route('organization-cards.edit', ['associations', $association->id]) }}"
                       class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                        Modifier
                    </a>
                </x-slot:actions>
            @endif
            <x-slot:meta>
                @if($association->short_name)<span>{{ $association->short_name }}</span>@endif
                @if($association->status)
                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">{{ ucfirst($association->status) }}</span>
                @endif
            </x-slot:meta>
        </x-page-header>

        <!-- Association Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header de l'association -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-2xl font-bold text-gray-800">{{ $association->name }}</h2>
                                @if($association->short_name)
                                    <span class="text-lg font-medium text-green-600 bg-green-100 px-3 py-1 rounded-full">
                                        {{ $association->short_name }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-lg text-gray-600">{{ $association->country }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 rounded-full text-sm font-medium {{ $association->status === 'active' ? 'bg-green-100 text-green-800' : ($association->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $association->status === 'active' ? 'Active' : ($association->status === 'inactive' ? 'Inactive' : 'En attente') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Logo et drapeau de l'association -->
                    <div class="flex justify-center items-center space-x-6 mb-6">
                        <!-- Logo de l'association -->
                        <div class="text-center">
                            <h4 class="text-sm font-medium text-gray-600 mb-2">Logo Association</h4>
                            @if($association->association_logo_url)
                                <img src="{{ \Illuminate\Support\Str::startsWith($association->association_logo_url, ['http://', 'https://']) ? $association->association_logo_url : asset('storage/' . $association->association_logo_url) }}" 
                                     alt="Logo {{ $association->name }}" 
                                     class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200">
                            @elseif($association->logo_path)
                                <img src="{{ asset('storage/' . $association->logo_path) }}" 
                                     alt="Logo {{ $association->name }}" 
                                     class="h-24 w-24 object-contain rounded-lg border-2 border-gray-200">
                            @else
                                <div class="h-24 w-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-2xl">{{ substr($association->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Drapeau du pays -->
                        <div class="text-center">
                            <h4 class="text-sm font-medium text-gray-600 mb-2">Drapeau Pays</h4>
                            @if($association->nation_flag_url)
                                <img src="{{ \Illuminate\Support\Str::startsWith($association->nation_flag_url, ['http://', 'https://']) ? $association->nation_flag_url : asset('storage/' . $association->nation_flag_url) }}" 
                                     alt="Drapeau {{ $association->country }}" 
                                     class="h-24 w-16 object-cover rounded-lg border-2 border-gray-200">
                            @else
                                @php
                                    $countryCode = '';
                                    if($association->country) {
                                        if(stripos($association->country, 'France') !== false) {
                                            $countryCode = 'FR';
                                            $flagColors = 'from-blue-500 via-white to-red-500';
                                        } elseif(stripos($association->country, 'Angleterre') !== false || stripos($association->country, 'England') !== false) {
                                            $countryCode = 'GB';
                                            $flagColors = 'from-blue-600 via-white to-red-600';
                                        } elseif(stripos($association->country, 'Tunisie') !== false) {
                                            $countryCode = 'TN';
                                            $flagColors = 'from-red-500 via-white to-red-500';
                                        } else {
                                            $countryCode = substr($association->country, 0, 2);
                                            $flagColors = 'from-blue-500 to-white';
                                        }
                                    } else {
                                        $countryCode = 'XX';
                                        $flagColors = 'from-gray-400 to-gray-600';
                                    }
                                @endphp
                                <div class="h-24 w-16 bg-gradient-to-br {{ $flagColors }} rounded-lg flex items-center justify-center border-2 border-gray-200">
                                    <span class="text-gray-800 font-bold text-lg">{{ $countryCode }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informations de base -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🌍</span>
                            <div>
                                @php($confederationName = \App\Models\Confederation::where('short_name', $association->confederation)->value('name'))
                                <p class="text-sm text-gray-500">{{ __('Confédération') }}</p>
                                <p class="font-medium">{{ $confederationName ?? $association->confederation ?? __('Non spécifiée') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🏆</span>
                            <div>
                                <p class="text-sm text-gray-500">Classement FIFA</p>
                                <p class="font-medium">{{ $association->fifa_ranking ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">🏟️</span>
                            <div>
                                <p class="text-sm text-gray-500">{{ __("Clubs affiliés") }}</p>
                                <p class="font-medium">{{ $association->clubs ? $association->clubs->count() : 0 }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="w-6 h-6 mr-3 text-green-600">👥</span>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('common.players') }}</p>
                                <p class="font-medium">{{ $association->players ? $association->players->count() : 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations FIFA Connect -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">⚽</span>
                        {{ __('Informations FIFA Connect') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-800 mb-3">{{ __('competition_management.sync_status') }}</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-green-600">{{ __('clinical.status_label') }}</span>
                                    <span class="font-medium {{ $association->fifa_sync_status === 'synced' ? 'text-green-600' : ($association->fifa_sync_status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                                        {{ $association->fifa_sync_status ?? 'pending' }}
                                    </span>
                                </div>
                                @if($association->fifa_sync_date)
                                <div class="flex justify-between">
                                    <span class="text-green-600">{{ __('Dernière sync:') }}</span>
                                    <span class="font-medium">{{ $association->fifa_sync_date->format('d/m/Y H:i') }}</span>
                                </div>
                                @endif
                                @if($association->fifa_version)
                                <div class="flex justify-between">
                                    <span class="text-green-600">Version FIFA:</span>
                                    <span class="font-medium">{{ $association->fifa_version }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">Contact</h4>
                            <div class="space-y-2">
                                @if($association->phone)
                                <div class="flex justify-between">
                                    <span class="text-blue-600">{{ __('📞 Téléphone:') }}</span>
                                    <span class="font-medium">{{ $association->phone }}</span>
                                </div>
                                @endif
                                @if($association->email)
                                <div class="flex justify-between">
                                    <span class="text-blue-600">📧 {{ __("Email:") }}</span>
                                    <span class="font-medium">{{ $association->email }}</span>
                                </div>
                                @endif
                                @if($association->website)
                                <div class="flex justify-between">
                                    <span class="text-blue-600">🌐 Site web:</span>
                                    <a href="{{ $association->website }}" target="_blank" class="font-medium text-blue-600 hover:underline">{{ $association->website }}</a>
                                </div>
                                @endif
                                @if($association->address)
                                <div class="flex justify-between">
                                    <span class="text-blue-600">🏟️ {{ __("Adresse:") }}</span>
                                    <span class="font-medium">{{ $association->address }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- {{ __("Clubs affiliés") }} -->
                @if($association->clubs && $association->clubs->count() > 0)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="mr-2">🏟️</span>
                        {{ __("Clubs affiliés") }} ({{ $association->clubs->count() }})
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($association->clubs as $club)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $club->getLogoUrl() }}"
                                     alt="Logo {{ $club->name }}"
                                     onerror="this.onerror=null;this.src='{{ asset('images/defaults/club-logo.png') }}'"
                                     class="h-12 w-12 object-contain rounded-lg border border-gray-200 bg-white">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ $club->name }}</h4>
                                    @if($club->short_name)
                                        <p class="text-sm text-blue-600">{{ $club->short_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $club->city ?? 'N/A' }}, {{ $club->country ?? 'Tunisie' }}</p>
                                </div>
                                <a href="{{ route('modules.clubs.show', $club->id) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                    {{ __('clinical.view_button') }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="/clubs-view?association_id={{ $association->id }}" 
                           class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            {{ __('🏟️ Voir tous les clubs de cette association') }}
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistiques -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('📊 Statistiques') }}</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $association->clubs ? $association->clubs->count() : 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __("Clubs affiliés") }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $association->players ? $association->players->count() : 0 }}</div>
                            <div class="text-sm text-gray-600">{{ __('Joueurs total') }}</div>
                        </div>
                        @if($association->fifa_ranking)
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ $association->fifa_ranking }}</div>
                            <div class="text-sm text-gray-600">Classement FIFA</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Actions rapides</h3>
                    <div class="space-y-3">
                        @if(\App\Http\Controllers\OrganizationCardController::canEdit(auth()->user(), 'associations', $association))
                        <a href="{{ route('organization-cards.edit', ['associations', $association->id]) }}"
                           class="w-full px-4 py-2 bg-yellow-500 text-white text-center rounded-lg hover:bg-yellow-600 transition-colors">
                            {{ __('✏️ Modifier l\'association') }}
                        </a>
                @endif
                        <a href="/clubs-view?association_id={{ $association->id }}" 
                           class="w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors">{{ __('🏟️ Gérer les clubs') }}</a>
                        <a href="/associations-view" 
                           class="w-full px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors">
                            {{ __('🏛️ Voir toutes les associations') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



