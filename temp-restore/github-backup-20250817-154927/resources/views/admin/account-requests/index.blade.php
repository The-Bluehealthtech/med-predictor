@extends('layouts.app')

@section('title', app()->getLocale() === 'fr' ? 'Gestion des Demandes de Compte' : 'Account Request Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ app()->getLocale() === 'fr' ? 'Gestion des Demandes de Compte' : 'Account Request Management' }}
        </h1>
        <p class="text-gray-600">
            {{ app()->getLocale() === 'fr' 
                ? 'Examinez et gérez les demandes de compte soumises par les utilisateurs.' 
                : 'Review and manage account requests submitted by users.' }}
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}
                </label>
                <select id="status-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">{{ app()->getLocale() === 'fr' ? 'Tous les statuts' : 'All Statuses' }}</option>
                    <option value="pending">{{ app()->getLocale() === 'fr' ? 'En attente' : 'Pending' }}</option>
                    <option value="contacted">{{ app()->getLocale() === 'fr' ? 'Contacté' : 'Contacted' }}</option>
                    <option value="approved">{{ app()->getLocale() === 'fr' ? 'Approuvé' : 'Approved' }}</option>
                    <option value="rejected">{{ app()->getLocale() === 'fr' ? 'Rejeté' : 'Rejected' }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Type d\'organisation' : 'Organization Type' }}
                </label>
                <select id="org-type-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">{{ app()->getLocale() === 'fr' ? 'Tous les types' : 'All Types' }}</option>
                    <option value="club">Club</option>
                    <option value="association">{{ app()->getLocale() === 'fr' ? 'Association' : 'Association' }}</option>
                    <option value="federation">{{ app()->getLocale() === 'fr' ? 'Fédération' : 'Federation' }}</option>
                    <option value="league">{{ app()->getLocale() === 'fr' ? 'Ligue' : 'League' }}</option>
                    <option value="academy">{{ app()->getLocale() === 'fr' ? 'Académie' : 'Academy' }}</option>
                    <option value="other">{{ app()->getLocale() === 'fr' ? 'Autre' : 'Other' }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Type de football' : 'Football Type' }}
                </label>
                <select id="football-type-filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    <option value="">{{ app()->getLocale() === 'fr' ? 'Tous les types' : 'All Types' }}</option>
                    <option value="11-a-side">Football 11 à 11</option>
                    <option value="futsal">Futsal</option>
                    <option value="women">{{ app()->getLocale() === 'fr' ? 'Football Féminin' : 'Women\'s Football' }}</option>
                    <option value="beach-soccer">Beach Soccer</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Rechercher' : 'Search' }}
                </label>
                <input type="text" id="search-filter" placeholder="{{ app()->getLocale() === 'fr' ? 'Nom, email, organisation...' : 'Name, email, organization...' }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2">
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Demandeur' : 'Applicant' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Organisation' : 'Organization' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Type' : 'Type' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Date' : 'Date' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ app()->getLocale() === 'fr' ? 'Actions' : 'Actions' }}
                        </th>
                    </tr>
                </thead>
                <tbody id="requests-tbody" class="bg-white divide-y divide-gray-200">
                    <!-- Requests will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="pagination" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <!-- Pagination will be loaded here -->
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ app()->getLocale() === 'fr' ? 'Chargement...' : 'Loading...' }}
        </div>
    </div>
</div>

<!-- Request Detail Modal -->
<div id="request-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div id="modal-content">
            <!-- Modal content will be loaded here -->
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approval-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                {{ app()->getLocale() === 'fr' ? 'Approuver la demande' : 'Approve Request' }}
            </h3>
            <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="approval-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Notes (optionnel)' : 'Notes (optional)' }}
                </label>
                <textarea id="approval-notes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    {{ app()->getLocale() === 'fr' ? 'Annuler' : 'Cancel' }}
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    {{ app()->getLocale() === 'fr' ? 'Approuver' : 'Approve' }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejection-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                {{ app()->getLocale() === 'fr' ? 'Rejeter la demande' : 'Reject Request' }}
            </h3>
            <button onclick="closeRejectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="rejection-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ app()->getLocale() === 'fr' ? 'Raison du rejet *' : 'Rejection Reason *' }}
                </label>
                <textarea id="rejection-reason" rows="3" required class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeRejectionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    {{ app()->getLocale() === 'fr' ? 'Annuler' : 'Cancel' }}
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    {{ app()->getLocale() === 'fr' ? 'Rejeter' : 'Reject' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentRequestId = null;

// Load requests on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
    
    // Add event listeners for filters
    document.getElementById('status-filter').addEventListener('change', loadRequests);
    document.getElementById('org-type-filter').addEventListener('change', loadRequests);
    document.getElementById('football-type-filter').addEventListener('change', loadRequests);
    document.getElementById('search-filter').addEventListener('input', debounce(loadRequests, 500));
    
    // Add form event listeners
    document.getElementById('approval-form').addEventListener('submit', approveRequest);
    document.getElementById('rejection-form').addEventListener('submit', rejectRequest);
});

function loadRequests(page = 1) {
    currentPage = page;
    const loading = document.getElementById('loading');
    const tbody = document.getElementById('requests-tbody');
    
    loading.classList.remove('hidden');
    tbody.innerHTML = '';
    
    const params = new URLSearchParams({
        page: page,
        status: document.getElementById('status-filter').value,
        organization_type: document.getElementById('org-type-filter').value,
        football_type: document.getElementById('football-type-filter').value,
        search: document.getElementById('search-filter').value
    });
    
    fetch(`/admin/account-requests?${params}`)
        .then(response => response.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success) {
                renderRequests(data.data);
                renderPagination(data.data);
            } else {
                showError('{{ app()->getLocale() === "fr" ? "Erreur lors du chargement des demandes" : "Error loading requests" }}');
            }
        })
        .catch(error => {
            loading.classList.add('hidden');
            console.error('Error:', error);
            showError('{{ app()->getLocale() === "fr" ? "Erreur lors du chargement des demandes" : "Error loading requests" }}');
        });
}

function renderRequests(data) {
    const tbody = document.getElementById('requests-tbody');
    
    if (data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    {{ app()->getLocale() === 'fr' ? 'Aucune demande trouvée' : 'No requests found' }}
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = data.data.map(request => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div>
                    <div class="text-sm font-medium text-gray-900">${request.first_name} ${request.last_name}</div>
                    <div class="text-sm text-gray-500">${request.email}</div>
                    ${request.phone ? `<div class="text-sm text-gray-500">${request.phone}</div>` : ''}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div>
                    <div class="text-sm font-medium text-gray-900">${request.organization_name}</div>
                    <div class="text-sm text-gray-500">${request.organization_type_label}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${request.football_type_label}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(request.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${new Date(request.created_at).toLocaleDateString()}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="viewRequest(${request.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                    {{ app()->getLocale() === 'fr' ? 'Voir' : 'View' }}
                </button>
                ${getActionButtons(request)}
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ app()->getLocale() === "fr" ? "En attente" : "Pending" }}</span>',
        'contacted': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ app()->getLocale() === "fr" ? "Contacté" : "Contacted" }}</span>',
        'approved': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ app()->getLocale() === "fr" ? "Approuvé" : "Approved" }}</span>',
        'rejected': '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ app()->getLocale() === "fr" ? "Rejeté" : "Rejected" }}</span>'
    };
    return badges[status] || status;
}

function getActionButtons(request) {
    if (request.status === 'pending') {
        return `
            <button onclick="markAsContacted(${request.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                {{ app()->getLocale() === 'fr' ? 'Contacter' : 'Contact' }}
            </button>
            <button onclick="openApprovalModal(${request.id})" class="text-green-600 hover:text-green-900 mr-3">
                {{ app()->getLocale() === 'fr' ? 'Approuver' : 'Approve' }}
            </button>
            <button onclick="openRejectionModal(${request.id})" class="text-red-600 hover:text-red-900">
                {{ app()->getLocale() === 'fr' ? 'Rejeter' : 'Reject' }}
            </button>
        `;
    } else if (request.status === 'contacted') {
        return `
            <button onclick="openApprovalModal(${request.id})" class="text-green-600 hover:text-green-900 mr-3">
                {{ app()->getLocale() === 'fr' ? 'Approuver' : 'Approve' }}
            </button>
            <button onclick="openRejectionModal(${request.id})" class="text-red-600 hover:text-red-900">
                {{ app()->getLocale() === 'fr' ? 'Rejeter' : 'Reject' }}
            </button>
        `;
    }
    return '';
}

function renderPagination(data) {
    const pagination = document.getElementById('pagination');
    
    if (data.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let paginationHtml = '<div class="flex items-center justify-between">';
    
    // Previous button
    if (data.current_page > 1) {
        paginationHtml += `<button onclick="loadRequests(${data.current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">{{ app()->getLocale() === 'fr' ? 'Précédent' : 'Previous' }}</button>`;
    } else {
        paginationHtml += '<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">{{ app()->getLocale() === 'fr' ? 'Précédent' : 'Previous' }}</button>';
    }
    
    // Page numbers
    paginationHtml += '<div class="flex space-x-2">';
    for (let i = 1; i <= data.last_page; i++) {
        if (i === data.current_page) {
            paginationHtml += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-white bg-indigo-600">${i}</span>`;
        } else {
            paginationHtml += `<button onclick="loadRequests(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">${i}</button>`;
        }
    }
    paginationHtml += '</div>';
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHtml += `<button onclick="loadRequests(${data.current_page + 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">{{ app()->getLocale() === 'fr' ? 'Suivant' : 'Next' }}</button>`;
    } else {
        paginationHtml += '<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-white cursor-not-allowed">{{ app()->getLocale() === 'fr' ? 'Suivant' : 'Next' }}</button>';
    }
    
    paginationHtml += '</div>';
    pagination.innerHTML = paginationHtml;
}

function viewRequest(id) {
    fetch(`/admin/account-requests/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showRequestModal(data.data);
            } else {
                showError('{{ app()->getLocale() === "fr" ? "Erreur lors du chargement de la demande" : "Error loading request" }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('{{ app()->getLocale() === "fr" ? "Erreur lors du chargement de la demande" : "Error loading request" }}');
        });
}

function showRequestModal(request) {
    const modal = document.getElementById('request-modal');
    const content = document.getElementById('modal-content');
    
    content.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                {{ app()->getLocale() === 'fr' ? 'Détails de la demande' : 'Request Details' }}
            </h3>
            <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Prénom' : 'First Name' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.first_name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Nom' : 'Last Name' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.last_name}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">${request.email}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Téléphone' : 'Phone' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.phone || '{{ app()->getLocale() === "fr" ? "Non fourni" : "Not provided" }}'}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Organisation' : 'Organization' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.organization_name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Type d\'organisation' : 'Organization Type' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.organization_type_label}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Type de football' : 'Football Type' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.football_type_label}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Pays' : 'Country' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.country}</p>
                </div>
            </div>
            ${request.city ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Ville' : 'City' }}</label>
                <p class="mt-1 text-sm text-gray-900">${request.city}</p>
            </div>
            ` : ''}
            ${request.description ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Description' : 'Description' }}</label>
                <p class="mt-1 text-sm text-gray-900">${request.description}</p>
            </div>
            ` : ''}
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}</label>
                <p class="mt-1">${getStatusBadge(request.status)}</p>
            </div>
            ${request.admin_notes ? `
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Notes administrateur' : 'Admin Notes' }}</label>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">${request.admin_notes}</p>
            </div>
            ` : ''}
            ${request.generated_username ? `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Nom d\'utilisateur généré' : 'Generated Username' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${request.generated_username}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'fr' ? 'Compte créé le' : 'Account Created' }}</label>
                    <p class="mt-1 text-sm text-gray-900">${new Date(request.user_created_at).toLocaleString()}</p>
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeRequestModal() {
    document.getElementById('request-modal').classList.add('hidden');
}

function openApprovalModal(id) {
    currentRequestId = id;
    document.getElementById('approval-notes').value = '';
    document.getElementById('approval-modal').classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approval-modal').classList.add('hidden');
    currentRequestId = null;
}

function openRejectionModal(id) {
    currentRequestId = id;
    document.getElementById('rejection-reason').value = '';
    document.getElementById('rejection-modal').classList.remove('hidden');
}

function closeRejectionModal() {
    document.getElementById('rejection-modal').classList.add('hidden');
    currentRequestId = null;
}

function approveRequest(e) {
    e.preventDefault();
    
    const notes = document.getElementById('approval-notes').value;
    
    fetch(`/admin/account-requests/${currentRequestId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeApprovalModal();
            loadRequests(currentPage);
            showSuccess('{{ app()->getLocale() === "fr" ? "Demande approuvée avec succès" : "Request approved successfully" }}');
        } else {
            showError(data.message || '{{ app()->getLocale() === "fr" ? "Erreur lors de l\'approbation" : "Error approving request" }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('{{ app()->getLocale() === "fr" ? "Erreur lors de l\'approbation" : "Error approving request" }}');
    });
}

function rejectRequest(e) {
    e.preventDefault();
    
    const reason = document.getElementById('rejection-reason').value;
    
    if (!reason.trim()) {
        showError('{{ app()->getLocale() === "fr" ? "Veuillez fournir une raison de rejet" : "Please provide a rejection reason" }}');
        return;
    }
    
    fetch(`/admin/account-requests/${currentRequestId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeRejectionModal();
            loadRequests(currentPage);
            showSuccess('{{ app()->getLocale() === "fr" ? "Demande rejetée avec succès" : "Request rejected successfully" }}');
        } else {
            showError(data.message || '{{ app()->getLocale() === "fr" ? "Erreur lors du rejet" : "Error rejecting request" }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('{{ app()->getLocale() === "fr" ? "Erreur lors du rejet" : "Error rejecting request" }}');
    });
}

function markAsContacted(id) {
    if (!confirm('{{ app()->getLocale() === "fr" ? "Marquer cette demande comme contactée ?" : "Mark this request as contacted?" }}')) {
        return;
    }
    
    fetch(`/admin/account-requests/${id}/contact`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadRequests(currentPage);
            showSuccess('{{ app()->getLocale() === "fr" ? "Demande marquée comme contactée" : "Request marked as contacted" }}');
        } else {
            showError(data.message || '{{ app()->getLocale() === "fr" ? "Erreur lors de la mise à jour" : "Error updating request" }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('{{ app()->getLocale() === "fr" ? "Erreur lors de la mise à jour" : "Error updating request" }}');
    });
}

function showSuccess(message) {
    // You can implement a toast notification here
    alert(message);
}

function showError(message) {
    // You can implement a toast notification here
    alert(message);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush 