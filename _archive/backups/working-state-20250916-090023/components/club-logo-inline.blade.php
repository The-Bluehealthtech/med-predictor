@props([
    'club' => null,
    'size' => 'small' // small, medium
])

@php
    // Définir les tailles
    $logoSizes = [
        'small' => 'w-6 h-6',
        'medium' => 'w-8 h-8'
    ];
    
    $logoSize = $logoSizes[$size] ?? $logoSizes['small'];
    
    // Vérifier si le club a un logo
    $hasLogo = $club && ($club->logo_url || $club->logo_path);
    $logoUrl = $club->logo_url ?? $club->logo_path ?? null;
    
    // Fallback : générer un logo avec les initiales
    if (!$hasLogo && $club) {
        $initials = getClubInitials($club->name);
        $logoUrl = "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=1e40af&color=ffffff&size=64&font-size=0.4&bold=true";
    }
@endphp

<div class="flex items-center space-x-2">
    @if($club)
        <div class="flex-shrink-0">
            @if($hasLogo)
                <img 
                    src="{{ $logoUrl }}"
                    alt="Logo {{ $club->name }}"
                    class="{{ $logoSize }} object-contain rounded shadow-sm"
                    title="{{ $club->name }}"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                />
                <div class="{{ $logoSize }} bg-gray-200 rounded flex items-center justify-center text-gray-600 font-bold text-xs" style="display: none;">
                    {{ getClubInitials($club->name) }}
                </div>
            @else
                <div class="{{ $logoSize }} bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">
                    {{ getClubInitials($club->name) }}
                </div>
            @endif
        </div>
        <span class="text-sm text-gray-900">{{ $club->name }}</span>
    @else
        <div class="{{ $logoSize }} bg-gray-300 rounded flex items-center justify-center text-gray-600 font-bold text-xs">
            ?
        </div>
        <span class="text-sm text-gray-500">Club inconnu</span>
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







