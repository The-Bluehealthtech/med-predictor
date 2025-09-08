@props([
    'associationCode' => '',
    'associationName' => '',
    'size' => 'md',
    'style' => 'flat',
    'showFallback' => true,
    'rounded' => true,
    'shadow' => true,
    'class' => ''
])

@php
    // Classes CSS dynamiques harmonisées
    $sizeClasses = [
        'xs' => 'w-8 h-8',    /* 32px */
        'sm' => 'w-10 h-10',  /* 40px */
        'md' => 'w-12 h-12',  /* 48px - taille par défaut harmonisée */
        'lg' => 'w-16 h-16',  /* 64px */
        'xl' => 'w-20 h-20',  /* 80px */
        '2xl' => 'w-24 h-24'  /* 96px */
    ];
    
    $baseClasses = 'object-contain';
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $roundedClass = $rounded ? 'rounded' : '';
    $shadowClass = $shadow ? 'shadow-sm' : '';
    
    $logoClasses = trim("$baseClasses $sizeClass $roundedClass $shadowClass $class");
    
    // Mapper les tailles vers les tailles de l'API flagsapi.com
    $flagSizes = [
        'xs' => 16,
        'sm' => 24,
        'md' => 32,
        'lg' => 48,
        'xl' => 64,
        '2xl' => 96
    ];
    
    $flagSize = $flagSizes[$size] ?? 32;
    
    // URLs des drapeaux
    $flagUrl = $associationCode ? "https://flagsapi.com/" . strtoupper($associationCode) . "/{$style}/{$flagSize}.png" : null;
    $fallbackFlagUrl = "https://flagsapi.com/UN/{$style}/{$flagSize}.png"; // Drapeau ONU comme fallback
@endphp

<div class="fifa-association-logo inline-block">
    @if($flagUrl)
        <!-- Drapeau de l'association -->
        <img 
            src="{{ $flagUrl }}" 
            alt="Drapeau de {{ $associationName ?: $associationCode }}"
            class="{{ $logoClasses }} transition-opacity duration-200"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
        />
    @endif
    
    @if($showFallback)
        <!-- Drapeau générique en cas d'erreur -->
        <img 
            src="{{ $fallbackFlagUrl }}" 
            alt="Drapeau générique pour {{ $associationCode }}"
            class="{{ $logoClasses }} opacity-75"
            style="display: {{ $flagUrl ? 'none' : 'block' }};"
        />
    @endif
    
    @if(!$flagUrl)
        <!-- Placeholder si pas de code d'association -->
        <div class="{{ $logoClasses }} flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-medium rounded">
            🏆
        </div>
    @endif
</div>

<style>
.fifa-association-logo {
    position: relative;
}

.fifa-association-logo img {
    transition: opacity 0.2s ease-in-out;
}

/* Animation de chargement */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
