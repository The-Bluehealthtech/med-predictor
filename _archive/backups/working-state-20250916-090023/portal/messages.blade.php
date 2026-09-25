@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-envelope text-green-600 mr-2"></i>
                        Messagerie Interne
                    </h2>
                    <button id="new-message-btn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Nouveau message
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Liste des conversations -->
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversations</h3>
                            <div id="conversations-list" class="space-y-2">
                                <!-- Les conversations seront chargées ici -->
                            </div>
                        </div>
                    </div>

                    <!-- Zone de conversation -->
                    <div class="lg:col-span-2">
                        <div id="conversation-area" class="bg-gray-50 rounded-lg p-4 min-h-96">
                            <div id="no-conversation" class="text-center py-12">
                                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune conversation sélectionnée</h3>
                                <p class="text-gray-500">Sélectionnez une conversation pour commencer à discuter.</p>
                            </div>

                            <div id="conversation-content" class="hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="conversation-title" class="text-lg font-semibold text-gray-900"></h3>
                                    <button id="close-conversation" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Messages -->
                                <div id="messages-container" class="space-y-4 mb-4 max-h-96 overflow-y-auto">
                                    <!-- Les messages seront chargés ici -->
                                </div>

                                <!-- Formulaire d'envoi -->
                                <div id="message-form" class="border-t pt-4">
                                    <form id="send-message-form" class="flex space-x-2">
                                        <input type="text" id="message-content" 
                                               class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                               placeholder="Tapez votre message...">
                                        <button type="submit" 
                                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Message -->
<div id="new-message-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Nouveau message</h3>
                
                <form id="new-message-form">
                    <div class="mb-4">
                        <label for="receiver-select" class="block text-sm font-medium text-gray-700 mb-2">
                            Destinataire
                        </label>
                        <select id="receiver-select" required 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Sélectionner un destinataire</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="message-subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Sujet
                        </label>
                        <input type="text" id="message-subject" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="mb-4">
                        <label for="message-body" class="block text-sm font-medium text-gray-700 mb-2">
                            Message
                        </label>
                        <textarea id="message-body" rows="4" required
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancel-message" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentConversation = null;
    let users = [];
    let conversations = [];

    // Charger les utilisateurs
    function loadUsers() {
        fetch('/portal/messages/users')
            .then(response => response.json())
            .then(data => {
                users = data.users;
                updateReceiverSelect();
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Mettre à jour la liste des destinataires
    function updateReceiverSelect() {
        const select = document.getElementById('receiver-select');
        select.innerHTML = '<option value="">Sélectionner un destinataire</option>';
        
        users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = `${user.name} (${user.role})`;
            select.appendChild(option);
        });
    }

    // Charger les conversations
    function loadConversations() {
        fetch('/portal/messages')
            .then(response => response.json())
            .then(data => {
                conversations = data.messages.data;
                updateConversationsList();
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Mettre à jour la liste des conversations
    function updateConversationsList() {
        const container = document.getElementById('conversations-list');
        
        if (conversations.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">Aucune conversation</p>';
            return;
        }

        container.innerHTML = conversations.map(message => {
            const otherUser = message.sender_id === {{ auth()->id() }} ? message.receiver : message.sender;
            return `
                <div class="conversation-item p-3 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors ${currentConversation === otherUser.id ? 'bg-blue-100' : ''}"
                     onclick="loadConversation(${otherUser.id})">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${otherUser.name}</p>
                            <p class="text-xs text-gray-500 truncate">${message.subject}</p>
                            ${!message.is_read && message.receiver_id === {{ auth()->id() }} ? 
                                '<span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Charger une conversation
    window.loadConversation = function(userId) {
        currentConversation = userId;
        updateConversationsList();
        
        fetch(`/portal/messages/conversation/${userId}`)
            .then(response => response.json())
            .then(data => {
                displayConversation(data.messages, userId);
            })
            .catch(error => console.error('Erreur:', error));
    };

    // Afficher une conversation
    function displayConversation(messages, userId) {
        const otherUser = users.find(u => u.id === userId);
        document.getElementById('conversation-title').textContent = otherUser ? otherUser.name : 'Utilisateur';
        
        document.getElementById('no-conversation').classList.add('hidden');
        document.getElementById('conversation-content').classList.remove('hidden');

        const container = document.getElementById('messages-container');
        container.innerHTML = messages.map(message => `
            <div class="message-item ${message.sender_id === {{ auth()->id() }} ? 'text-right' : 'text-left'}">
                <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${message.sender_id === {{ auth()->id() }} ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-900'}">
                    <p class="text-sm">${message.content}</p>
                    <p class="text-xs ${message.sender_id === {{ auth()->id() }} ? 'text-green-100' : 'text-gray-500'} mt-1">
                        ${new Date(message.created_at).toLocaleString('fr-FR')}
                    </p>
                </div>
            </div>
        `).join('');

        container.scrollTop = container.scrollHeight;
    }

    // Envoyer un message
    document.getElementById('send-message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentConversation) return;

        const content = document.getElementById('message-content').value.trim();
        if (!content) return;

        fetch('/portal/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                receiver_id: currentConversation,
                subject: 'Message',
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('message-content').value = '';
            loadConversation(currentConversation);
            loadConversations();
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Nouveau message
    document.getElementById('new-message-btn').addEventListener('click', function() {
        document.getElementById('new-message-modal').classList.remove('hidden');
    });

    document.getElementById('cancel-message').addEventListener('click', function() {
        document.getElementById('new-message-modal').classList.add('hidden');
        document.getElementById('new-message-form').reset();
    });

    document.getElementById('new-message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            receiver_id: document.getElementById('receiver-select').value,
            subject: document.getElementById('message-subject').value,
            content: document.getElementById('message-body').value
        };

        fetch('/portal/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('new-message-modal').classList.add('hidden');
            document.getElementById('new-message-form').reset();
            loadConversations();
        })
        .catch(error => console.error('Erreur:', error));
    });

    // Fermer la conversation
    document.getElementById('close-conversation').addEventListener('click', function() {
        currentConversation = null;
        document.getElementById('no-conversation').classList.remove('hidden');
        document.getElementById('conversation-content').classList.add('hidden');
        updateConversationsList();
    });

    // Initialisation
    loadUsers();
    loadConversations();
});
</script>
@endpush
@endsection 