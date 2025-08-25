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
    
    if ($association) {
        $logoUrl = $association->getLogoUrl();
        $logoAlt = $association->getLogoAlt();
    }
    
    // Classes CSS
    $classes = "object-contain {$sizeClass} rounded shadow-sm {$class}";
@endphp

<div class="association-logo inline-block">
    @if($logoUrl)
        <img 
            src="{{ $logoUrl }}" 
            alt="{{ $logoAlt }}"
            class="{{ $classes }}"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            onload="this.style.opacity='1';"
        />
    @elseif($showFallback)
        <!-- Logo par défaut -->
        <div class="{{ $classes }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium" style="display: none;">
            🏆
        </div>
    @else
        <!-- Placeholder vide -->
        <div class="{{ $classes }} bg-transparent"></div>
    @endif
</div>

<style>
.association-logo {
    position: relative;
    display: inline-block;
}

.association-logo img {
    transition: all 0.2s ease-in-out;
    opacity: 0;
}

.association-logo img:hover {
    transform: scale(1.05);
}

.fallback-logo {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appliquer les gestionnaires d'événements
    document.querySelectorAll('.association-logo img').forEach(img => {
        img.style.transition = 'opacity 0.3s ease-in-out';
    });
});
</script>
