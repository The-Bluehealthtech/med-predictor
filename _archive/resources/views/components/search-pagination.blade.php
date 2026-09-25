@props([
    'searchFields' => [],
    'currentPage' => 1,
    'totalPages' => 1,
    'perPage' => 20,
    'totalItems' => 0,
    'searchParams' => []
])

<div class="bg-white shadow-sm rounded-lg mb-6">
    <!-- Barre de recherche -->
    <div class="p-6 border-b border-gray-200">
        <form method="GET" action="{{ request()->url() }}" class="space-y-4">
            <!-- Champs de recherche -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($searchFields as $field)
                    <div>
                        <label for="search_{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $field['label'] }}
                        </label>
                        @if($field['type'] === 'select')
                            <select name="{{ $field['name'] }}" id="search_{{ $field['name'] }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous</option>
                                @foreach($field['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ request($field['name']) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($field['type'] === 'date')
                            <input type="date" name="{{ $field['name'] }}" id="search_{{ $field['name'] }}" 
                                   value="{{ request($field['name']) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @elseif($field['type'] === 'date_range')
                            <div class="flex space-x-2">
                                <input type="date" name="{{ $field['name'] }}_from" 
                                       value="{{ request($field['name'] . '_from') }}"
                                       placeholder="De"
                                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="date" name="{{ $field['name'] }}_to" 
                                       value="{{ request($field['name'] . '_to') }}"
                                       placeholder="À"
                                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        @else
                            <input type="{{ $field['type'] ?? 'text' }}" 
                                   name="{{ $field['name'] }}" 
                                   id="search_{{ $field['name'] }}" 
                                   value="{{ request($field['name']) }}"
                                   placeholder="{{ $field['placeholder'] ?? 'Rechercher...' }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Rechercher
                    </button>
                    
                    <a href="{{ request()->url() }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Réinitialiser
                    </a>
                </div>

                <!-- Informations de pagination -->
                <div class="text-sm text-gray-700">
                    @if($totalItems > 0)
                        Affichage de {{ (($currentPage - 1) * $perPage) + 1 }} à {{ min($currentPage * $perPage, $totalItems) }} sur {{ $totalItems }} résultats
                    @else
                        Aucun résultat trouvé
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <!-- Pagination mobile -->
                    @if($currentPage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Précédent
                        </a>
                    @endif
                    
                    @if($currentPage < $totalPages)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Suivant
                        </a>
                    @endif
                </div>

                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <!-- Page précédente -->
                            @if($currentPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Précédent</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endif

                            <!-- Pages numérotées -->
                            @php
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                                @if($start > 2)
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                                   class="relative inline-flex items-center px-4 py-2 border text-sm font-medium {{ $i == $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if($end < $totalPages)
                                @if($end < $totalPages - 1)
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>
                                @endif
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $totalPages }}</a>
                            @endif

                            <!-- Page suivante -->
                            @if($currentPage < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Suivant</span>
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            @endif
                        </nav>
                    </div>

                    <!-- Informations de pagination -->
                    <div class="text-sm text-gray-700">
                        Page {{ $currentPage }} sur {{ $totalPages }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
