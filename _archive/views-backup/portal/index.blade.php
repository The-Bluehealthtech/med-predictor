@extends('layouts.app')

@section('title', 'Portail Athlète - Med Predictor')

@section('content')
<div id="athlete-portal-app">
    <!-- L'application Vue.js du portail athlète sera chargée ici -->
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Chargement du Portail Athlète...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Configuration du portail athlète
    window.ATHLETE_PORTAL_CONFIG = {
        apiBaseUrl: '{{ url("/api/v1/portal") }}',
        csrfToken: '{{ csrf_token() }}',
        athleteId: '{{ auth()->user()->athlete->id ?? null }}',
        fifaConnectId: '{{ auth()->user()->athlete->fifa_connect_id ?? null }}',
        locale: '{{ app()->getLocale() }}'
    };
</script>

<!-- Chargement de l'application Vue.js du portail athlète -->
<script type="module">
    import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';
    import AthletePortal from '{{ asset("js/portal/App.vue") }}';
    
    const app = createApp(AthletePortal);
    app.mount('#athlete-portal-app');
</script>
@endpush

@push('styles')
<style>
    /* Styles spécifiques au portail athlète */
    .athlete-portal {
        font-family: 'Inter', sans-serif;
    }
    
    .portal-nav {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .portal-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .portal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
@endsection 