@props([
    'association' => null,
    'size' => 'md',
    'showFallback' => true,
    'class' => '',
    'alt' => null
])

@php
    // Classes de taille
    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        '2xl' => 'w-20 h-20'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    // Déterminer l'URL du logo
    $logoUrl = null;
    $logoAlt = $alt ?? 'Logo de l\'association';
    
    if ($association && $association->country) {
        // Mappage simple des pays vers les codes
        $countryMapping = [
            'france' => 'FR',
            'maroc' => 'MA',
            'tunisie' => 'TN',
            'algérie' => 'DZ',
            'sénégal' => 'SN',
            'mali' => 'ML',
            'côte d\'ivoire' => 'CI',
            'nigeria' => 'NG',
            'ghana' => 'GH',
            'cameroun' => 'CM',
            'égypte' => 'EG',
            'ftf' => 'TN', // Fédération Tunisienne de Football
            'faf' => 'DZ', // Fédération Algérienne de Football
            'frmf' => 'MA', // Fédération Royale Marocaine de Football
        ];
        
        $countryCode = $countryMapping[strtolower($association->country)] ?? null;
        
        if ($countryCode) {
            $logoPath = public_path("associations/{$countryCode}.png");
            if (file_exists($logoPath)) {
                $logoUrl = asset("associations/{$countryCode}.png");
                $logoAlt = ($association->name ?? $association->country) . ' Association Logo';
            }
        }
    }
    
    // Classes CSS
    $classes = "object-contain {$sizeClass} rounded shadow-sm {$class}";
@endphp

<div class="association-logo-working inline-block">
    @if($logoUrl)
        <img 
            src="{{ $logoUrl }}" 
            alt="{{ $logoAlt }}"
            class="{{ $classes }}"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            onload="this.style.opacity='1';"
        />
        <!-- Fallback en cas d'erreur d'image -->
        <div class="{{ $classes }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium" style="display: none;">
            🏆
        </div>
    @elseif($showFallback)
        <!-- Logo par défaut -->
        <div class="{{ $classes }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium">
            🏆
        </div>
    @else
        <!-- Placeholder vide -->
        <div class="{{ $classes }} bg-transparent"></div>
    @endif
</div>

<style>
.association-logo-working {
    position: relative;
    display: inline-block;
}

.association-logo-working img {
    transition: all 0.2s ease-in-out;
    opacity: 0;
}

.association-logo-working img:hover {
    transform: scale(1.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appliquer les gestionnaires d'événements
    document.querySelectorAll('.association-logo-working img').forEach(img => {
        img.style.transition = 'opacity 0.3s ease-in-out';
    });
});
</script>

