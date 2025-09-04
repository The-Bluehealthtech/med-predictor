@extends('layouts.app')

@section('title', 'Dashboard Athlète - Portail Athlète')

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
                            <a href="{{ route('portal.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium bg-white bg-opacity-20">Dashboard</a>
                            <a href="{{ route('portal.wellness') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Bien-être</a>
                            <a href="{{ route('portal.devices') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Appareils</a>
                            <a href="{{ route('portal.medical-record') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Dossier Médical</a>
                            <a href="{{ route('portal.notifications.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 relative">
                                Notifications
                                <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                            </a>
                            <a href="{{ route('portal.messages.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20 relative">
                                Messages
                                <span id="message-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                            </a>
                            <a href="{{ route('portal.medical-appointments.index') }}" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white hover:bg-opacity-20">Rendez-vous</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <!-- Photo de profil de l'utilisateur -->
                        @if(auth()->user()->hasProfilePicture())
                            <img src="{{ auth()->user()->getProfilePictureUrl() }}" 
                                 alt="{{ auth()->user()->getProfilePictureAlt() }}" 
                                 class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                        @else
                            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-semibold border-2 border-white shadow-sm">
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
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- En-tête du Dashboard -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard Athlète</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Bienvenue, {{ auth()->user()->name }} - FIFA Connect ID: {{ auth()->user()->athlete->fifa_connect_id ?? 'Non défini' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Cartes de Résumé -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Statut de Santé -->
            <div class="portal-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 text-lg">❤️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Statut de Santé</p>
                        <p class="text-lg font-semibold text-gray-900">Excellent</p>
                    </div>
                </div>
            </div>

            <!-- Prochain Rendez-vous -->
            <div class="portal-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-lg">📅</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Prochain RDV</p>
                        <p class="text-lg font-semibold text-gray-900">15/08/2024</p>
                    </div>
                </div>
            </div>

            <!-- Appareils Connectés -->
            <div class="portal-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <span class="text-yellow-600 text-lg">📱</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Appareils</p>
                        <p class="text-lg font-semibold text-gray-900">2 connectés</p>
                    </div>
                </div>
            </div>

            <!-- Score de Bien-être -->
            <div class="portal-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 text-lg">📊</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Bien-être</p>
                        <p class="text-lg font-semibold text-gray-900">85/100</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Demander un Rendez-vous Médical -->
            <div class="portal-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Rendez-vous Médical</h3>
                        <p class="text-sm text-gray-600 mb-4">Demander une consultation sur site ou en télé médecine</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('portal.medical-appointments.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors text-sm">
                                <i class="fas fa-stethoscope mr-2"></i>
                                Demander RDV
                            </a>
                        </div>
                    </div>
                    <div class="text-4xl text-purple-600">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                </div>
            </div>

            <!-- Messagerie Interne -->
            <div class="portal-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Messagerie</h3>
                        <p class="text-sm text-gray-600 mb-4">Communiquer avec les membres de FIT</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('portal.messages.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm">
                                <i class="fas fa-envelope mr-2"></i>
                                Ouvrir Messages
                            </a>
                        </div>
                    </div>
                    <div class="text-4xl text-green-600">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="portal-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notifications</h3>
                        <p class="text-sm text-gray-600 mb-4">Consulter vos notifications récentes</p>
                        <div class="flex space-x-2">
                            <a href="{{ route('portal.notifications.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-bell mr-2"></i>
                                Voir Notifications
                            </a>
                        </div>
                    </div>
                    <div class="text-4xl text-blue-600">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité Récente -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notifications Récentes -->
            <div class="portal-card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notifications Récentes</h3>
                    <div id="recent-notifications" class="space-y-3">
                        <!-- Les notifications seront chargées ici -->
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('portal.notifications.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Voir toutes les notifications <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages Récents -->
            <div class="portal-card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Messages Récents</h3>
                    <div id="recent-messages" class="space-y-3">
                        <!-- Les messages seront chargés ici -->
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('portal.messages.index') }}" class="text-green-600 hover:text-green-800 text-sm">
                            Voir tous les messages <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Charger les compteurs de notifications et messages
    function loadCounters() {
        // Notifications non lues
        fetch('/portal/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notification-badge');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(error => console.error('Erreur:', error));

        // Messages non lus
        fetch('/portal/messages')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('message-badge');
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Charger les notifications récentes
    function loadRecentNotifications() {
        fetch('/portal/notifications?page=1')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recent-notifications');
                const notifications = data.notifications.data.slice(0, 3);
                
                if (notifications.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-sm">Aucune notification récente</p>';
                    return;
                }

                container.innerHTML = notifications.map(notification => `
                    <div class="flex items-start space-x-3 ${notification.is_read ? 'opacity-75' : ''}">
                        <div class="flex-shrink-0">
                            <i class="${notification.icon_class || 'fas fa-bell'} text-sm ${notification.status_color}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-xs text-gray-500 truncate">${notification.content}</p>
                            <p class="text-xs text-gray-400">${new Date(notification.created_at).toLocaleString('fr-FR')}</p>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Charger les messages récents
    function loadRecentMessages() {
        fetch('/portal/messages?page=1')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recent-messages');
                const messages = data.messages.data.slice(0, 3);
                
                if (messages.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-sm">Aucun message récent</p>';
                    return;
                }

                container.innerHTML = messages.map(message => `
                    <div class="flex items-start space-x-3 ${message.is_read ? 'opacity-75' : ''}">
                        <div class="flex-shrink-0">
                            <i class="fas fa-envelope text-sm ${message.is_read ? 'text-gray-400' : 'text-green-600'}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${message.subject}</p>
                            <p class="text-xs text-gray-500 truncate">${message.content}</p>
                            <p class="text-xs text-gray-400">${new Date(message.created_at).toLocaleString('fr-FR')}</p>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Initialisation
    loadCounters();
    loadRecentNotifications();
    loadRecentMessages();

    // Actualiser toutes les 30 secondes
    setInterval(() => {
        loadCounters();
    }, 30000);
});
</script>
@endpush

<style>
.portal-nav {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.portal-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.portal-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: translateY(-1px);
}
</style>
@endsection 