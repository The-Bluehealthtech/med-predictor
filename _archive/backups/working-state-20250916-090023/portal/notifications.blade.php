@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-bell text-blue-600 mr-2"></i>
                        Notifications
                    </h2>
                    <div class="flex space-x-2">
                        <button id="mark-all-read" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check-double mr-2"></i>
                            Tout marquer comme lu
                        </button>
                        <span id="unread-count" class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            0
                        </span>
                    </div>
                </div>

                <!-- Liste des notifications -->
                <div id="notifications-list" class="space-y-4">
                    <!-- Les notifications seront chargées ici -->
                </div>

                <!-- Pagination -->
                <div id="notifications-pagination" class="mt-6">
                    <!-- La pagination sera chargée ici -->
                </div>

                <!-- État de chargement -->
                <div id="loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    <p class="mt-2 text-gray-500">Chargement des notifications...</p>
                </div>

                <!-- État vide -->
                <div id="empty-state" class="text-center py-8 hidden">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune notification</h3>
                    <p class="text-gray-500">Vous n'avez pas encore de notifications.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let notifications = [];
    let unreadCount = 0;

    // Charger les notifications
    function loadNotifications(page = 1) {
        fetch(`/portal/notifications?page=${page}`)
            .then(response => response.json())
            .then(data => {
                notifications = data.notifications.data;
                unreadCount = data.unread_count;
                currentPage = page;
                
                updateNotificationsList();
                updateUnreadCount();
                updatePagination(data.notifications);
                hideLoading();
            })
            .catch(error => {
                console.error('Erreur lors du chargement des notifications:', error);
                hideLoading();
                showEmptyState();
            });
    }

    // Mettre à jour la liste des notifications
    function updateNotificationsList() {
        const container = document.getElementById('notifications-list');
        
        if (notifications.length === 0) {
            showEmptyState();
            return;
        }

        container.innerHTML = notifications.map(notification => `
            <div class="notification-item bg-gray-50 rounded-lg p-4 ${notification.is_read ? 'opacity-75' : 'border-l-4 border-blue-500'}">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="${notification.icon_class || 'fas fa-bell'} text-lg ${notification.status_color}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">${notification.title}</h4>
                            <p class="text-sm text-gray-600 mt-1">${notification.content}</p>
                            <p class="text-xs text-gray-400 mt-2">
                                ${new Date(notification.created_at).toLocaleString('fr-FR')}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        ${!notification.is_read ? `
                            <button onclick="markAsRead(${notification.id})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                        <button onclick="deleteNotification(${notification.id})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${notification.action_url ? `
                    <div class="mt-3">
                        <a href="${notification.action_url}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Voir plus <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    // Mettre à jour le compteur de notifications non lues
    function updateUnreadCount() {
        const counter = document.getElementById('unread-count');
        counter.textContent = unreadCount;
        counter.style.display = unreadCount > 0 ? 'block' : 'none';
    }

    // Mettre à jour la pagination
    function updatePagination(paginationData) {
        const container = document.getElementById('notifications-pagination');
        
        if (paginationData.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<div class="flex justify-center space-x-2">';
        
        // Bouton précédent
        if (paginationData.current_page > 1) {
            paginationHtml += `
                <button onclick="loadNotifications(${paginationData.current_page - 1})" 
                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
        }

        // Pages
        for (let i = 1; i <= paginationData.last_page; i++) {
            if (i === paginationData.current_page) {
                paginationHtml += `
                    <span class="px-3 py-2 bg-blue-600 text-white rounded-md">
                        ${i}
                    </span>
                `;
            } else {
                paginationHtml += `
                    <button onclick="loadNotifications(${i})" 
                            class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ${i}
                    </button>
                `;
            }
        }

        // Bouton suivant
        if (paginationData.current_page < paginationData.last_page) {
            paginationHtml += `
                <button onclick="loadNotifications(${paginationData.current_page + 1})" 
                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        }

        paginationHtml += '</div>';
        container.innerHTML = paginationHtml;
    }

    // Marquer une notification comme lue
    window.markAsRead = function(notificationId) {
        fetch(`/portal/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            unreadCount = data.unread_count;
            updateUnreadCount();
            loadNotifications(currentPage);
        })
        .catch(error => console.error('Erreur:', error));
    };

    // Supprimer une notification
    window.deleteNotification = function(notificationId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
            return;
        }

        fetch(`/portal/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadNotifications(currentPage);
        })
        .catch(error => console.error('Erreur:', error));
    };

    // Marquer toutes les notifications comme lues
    document.getElementById('mark-all-read').addEventListener('click', function() {
        fetch('/portal/notifications/read-all', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            unreadCount = 0;
            updateUnreadCount();
            loadNotifications(currentPage);
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Fonctions utilitaires
    function hideLoading() {
        document.getElementById('loading').style.display = 'none';
    }

    function showEmptyState() {
        document.getElementById('empty-state').classList.remove('hidden');
    }

    // Charger les notifications au démarrage
    loadNotifications();
});
</script>
@endpush
@endsection 