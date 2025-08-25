@props([
    'club' => null,
    'size' => 'medium', // small, medium, large
    'showName' => true,
    'showCountry' => false
])

@php
    // Définir les tailles
    $logoSizes = [
        'small' => 'w-8 h-8',
        'medium' => 'w-12 h-12', 
        'large' => 'w-16 h-16'
    ];
    
    $logoSize = $logoSizes[$size] ?? $logoSizes['medium'];
    
    // Vérifier si le club a un logo
    $hasLogo = $club && ($club->logo_url || $club->logo_path);
    $logoUrl = $club->logo_url ?? $club->logo_path ?? null;
    
    // Fallback : générer un logo avec les initiales
    if (!$hasLogo && $club) {
        $initials = getClubInitials($club->name);
        $logoUrl = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=1e40af&color=ffffff&size=128&font-size=0.5&bold=true";
    }
@endphp

<div class="flex items-center space-x-3">
    @if($club)
        <div class="flex-shrink-0">
            @if($hasLogo)
                <img 
                    src="{{ $logoUrl }}"
                    alt="Logo {{ $club->name }}"
                    class="{{ $logoSize }} object-contain rounded-lg shadow-sm"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                />
                <div class="{{ $logoSize }} bg-gray-200 rounded-lg flex items-center justify-center text-gray-600 font-bold text-xs" style="display: none;">
                    {{ getClubInitials($club->name) }}
                </div>
            @else
                <div class="{{ $logoSize }} bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                    {{ getClubInitials($club->name) }}
                </div>
            @endif
        </div>
        
        @if($showName)
            <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-900">{{ $club->name }}</span>
                @if($showCountry && $club->country)
                    <span class="text-xs text-gray-500">{{ $club->country }}</span>
                @endif
            </div>
        @endif
    @else
        <div class="{{ $logoSize }} bg-gray-300 rounded-lg flex items-center justify-center text-gray-600 font-bold text-xs">
            ?
        </div>
        @if($showName)
            <span class="text-sm text-gray-500">Club inconnu</span>
        @endif
    @endif
</div>

@php
function getClubInitials($clubName) {
    $words = explode(' ', $clubName);
    $initials = '';
    
    foreach ($words as $word) {
        if (strlen($word) > 0) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    
    return $initials;
}
@endphp




