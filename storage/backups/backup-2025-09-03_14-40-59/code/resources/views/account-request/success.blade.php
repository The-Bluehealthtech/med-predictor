@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Title -->
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                @if(app()->getLocale() == 'en')
                    Request Submitted Successfully!
                @else
                    Demande Soumise avec Succès !
                @endif
            </h2>

            <!-- Success Message -->
            <p class="text-lg text-gray-600 mb-6">
                {{ $message }}
            </p>

            <!-- Redirection Info -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    @if(app()->getLocale() == 'en')
                        You will be redirected to the homepage in a few seconds...
                    @else
                        Vous allez être redirigé vers la page d'accueil dans quelques secondes...
                    @endif
                </p>
            </div>

            <!-- Loading Spinner -->
            <div class="flex justify-center mb-6">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>

            <!-- Manual Redirect Button -->
            <a href="{{ route('landing') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                @if(app()->getLocale() == 'en')
                    Go to Homepage
                @else
                    Aller à l'Accueil
                @endif
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
// Redirection automatique après 3 secondes
setTimeout(() => {
    window.location.href = '{{ route("landing") }}';
}, 3000);
</script>
@endsection
