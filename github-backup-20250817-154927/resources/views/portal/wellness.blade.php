@extends('layouts.app')

@section('title', 'Formulaire de Bien-être - Portail Athlète')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Navigation du Portail Athlète -->
    <nav class="portal-nav text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold">🏃‍♂️ Portail Athlète</h1>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ route('portal.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Dashboard</a>
                            <a href="{{ route('portal.wellness') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-white bg-opacity-20">Bien-être</a>
                            <a href="{{ route('portal.devices') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Appareils</a>
                            <a href="{{ route('portal.medical-record') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Dossier Médical</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <!-- Photo de profil de l'utilisateur -->
                        @if(auth()->user()->hasProfilePicture())
                            <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                 alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                 class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold border-2 border-white shadow-sm">
                                {{ auth()->user()->getInitials() }}
                            </div>
                        @endif
                        <span class="text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu Principal -->
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Formulaire de Bien-être</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Soumettez votre évaluation quotidienne de bien-être pour suivre votre santé et performance.
                </p>
            </div>

            <!-- Formulaire de Bien-être -->
            <div class="portal-card p-6">
                <form id="wellness-form" class="space-y-6">
                    @csrf
                    
                    <!-- Date et Heure -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date et Heure
                        </label>
                        <input type="datetime-local" name="assessment_date" value="{{ now()->format('Y-m-d\TH:i') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <!-- Échelle de Bien-être Général -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Comment vous sentez-vous aujourd'hui ? (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Très mal)</span>
                            <input type="range" name="general_wellness" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Excellent)</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span><span>10</span>
                        </div>
                    </div>

                    <!-- Qualité du Sommeil -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Qualité du sommeil (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Mauvaise)</span>
                            <input type="range" name="sleep_quality" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Excellente)</span>
                        </div>
                    </div>

                    <!-- Niveau d'Énergie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau d'énergie (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Fatigué)</span>
                            <input type="range" name="energy_level" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Énergique)</span>
                        </div>
                    </div>

                    <!-- Stress -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau de stress (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Détendu)</span>
                            <input type="range" name="stress_level" min="1" max="10" value="4" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Stressé)</span>
                        </div>
                    </div>

                    <!-- Douleurs ou Blessures -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Avez-vous des douleurs ou blessures ?
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="pain_injury" value="none" class="mr-2" checked>
                                <span class="text-sm">Aucune douleur ou blessure</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pain_injury" value="minor" class="mr-2">
                                <span class="text-sm">Douleur mineure</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pain_injury" value="moderate" class="mr-2">
                                <span class="text-sm">Douleur modérée</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pain_injury" value="severe" class="mr-2">
                                <span class="text-sm">Douleur sévère</span>
                            </label>
                        </div>
                    </div>

                    <!-- Description des Symptômes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description des symptômes (optionnel)
                        </label>
                        <textarea name="symptoms_description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Décrivez vos symptômes ou préoccupations..."></textarea>
                    </div>

                    <!-- Hydratation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau d'hydratation (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Déshydraté)</span>
                            <input type="range" name="hydration_level" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Bien hydraté)</span>
                        </div>
                    </div>

                    <!-- Appétit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Appétit (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Pas d'appétit)</span>
                            <input type="range" name="appetite_level" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Excellent appétit)</span>
                        </div>
                    </div>

                    <!-- Motivation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau de motivation (1-10)
                        </label>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">1 (Démotivé)</span>
                            <input type="range" name="motivation_level" min="1" max="10" value="7" 
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" required>
                            <span class="text-sm text-gray-500">10 (Très motivé)</span>
                        </div>
                    </div>

                    <!-- Notes Additionnelles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes additionnelles (optionnel)
                        </label>
                        <textarea name="additional_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Ajoutez des commentaires ou observations..."></textarea>
                    </div>

                    <!-- Boutons d'Action -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('portal.dashboard') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Soumettre le Formulaire
                        </button>
                    </div>
                </form>
            </div>

            <!-- Historique des Soumissions -->
            <div class="portal-card p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des Soumissions</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Soumission du 03/08/2024</p>
                            <p class="text-xs text-gray-500">Bien-être: 8/10 | Sommeil: 7/10 | Énergie: 8/10</p>
                        </div>
                        <span class="text-green-600 text-sm">✅ Soumis</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Soumission du 02/08/2024</p>
                            <p class="text-xs text-gray-500">Bien-être: 7/10 | Sommeil: 6/10 | Énergie: 7/10</p>
                        </div>
                        <span class="text-green-600 text-sm">✅ Soumis</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Soumission du 01/08/2024</p>
                            <p class="text-xs text-gray-500">Bien-être: 6/10 | Sommeil: 5/10 | Énergie: 6/10</p>
                        </div>
                        <span class="text-green-600 text-sm">✅ Soumis</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
document.getElementById('wellness-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    try {
        submitButton.disabled = true;
        submitButton.textContent = 'Soumission en cours...';
        
        const response = await fetch('/api/v1/portal/wellness-form', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        if (response.ok) {
            alert('Formulaire de bien-être soumis avec succès !');
            window.location.href = '{{ route("portal.dashboard") }}';
        } else {
            throw new Error('Erreur lors de la soumission');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la soumission du formulaire. Veuillez réessayer.');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});
</script>
@endpush
@endsection 