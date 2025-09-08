@props([
    'countryCode' => null,
    'countryName' => '',
    'size' => 'md',
    'format' => 'svg',
    'fallback' => true,
    'rounded' => true,
    'shadow' => true,
    'class' => ''
])

@php
    // Validation du code pays
    $isValidCountryCode = $countryCode && preg_match('/^[A-Z]{2}$/i', $countryCode);
    
    // Classes CSS dynamiques
    $sizeClasses = [
        'xs' => 'w-4 h-3',
        'sm' => 'w-6 h-4', 
        'md' => 'w-8 h-6',
        'lg' => 'w-12 h-8',
        'xl' => 'w-16 h-12',
        '2xl' => 'w-20 h-15'
    ];
    
    $baseClasses = 'object-cover';
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $roundedClass = $rounded ? 'rounded-sm' : '';
    $shadowClass = $shadow ? 'shadow-sm' : '';
    
    $flagClasses = trim("$baseClasses $sizeClass $roundedClass $shadowClass $class");
    
    // URL du drapeau
    $flagUrl = null;
    if ($isValidCountryCode) {
        $code = strtolower($countryCode);
        if ($format === 'svg') {
            $flagUrl = "https://flagcdn.com/$code.svg";
        } else {
            $pngSizes = [
                'xs' => 'w40',
                'sm' => 'w80',
                'md' => 'w120',
                'lg' => 'w160',
                'xl' => 'w240',
                '2xl' => 'w320'
            ];
            $pngSize = $pngSizes[$size] ?? $pngSizes['md'];
            $flagUrl = "https://flagcdn.com/$pngSize/$code.png";
        }
    }
    
    // Classes pour le fallback
    $fallbackClasses = trim("$sizeClass flex items-center justify-center bg-gray-200 text-gray-500 font-medium");
    $fallbackTextSize = [
        'xs' => 'text-xs',
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg',
        '2xl' => 'text-xl'
    ];
    $fallbackClasses .= ' ' . ($fallbackTextSize[$size] ?? $fallbackTextSize['md']);
@endphp

<div class="country-flag-container inline-block">
    @if($flagUrl)
        <img 
            src="{{ $flagUrl }}" 
            alt="Drapeau de {{ $countryName ?: $countryCode }}"
            class="{{ $flagClasses }} transition-opacity duration-200"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            onload="this.style.opacity='1';"
            style="opacity: 0;"
        />
    @endif
    
    @if($fallback)
        <div 
            class="{{ $fallbackClasses }}"
            style="display: {{ $flagUrl ? 'none' : 'flex' }};"
        >
            {{ $countryCode ? strtoupper($countryCode) : '🏳️' }}
        </div>
    @endif
</div>

<style>
.country-flag-container {
    position: relative;
}

.country-flag-container img {
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

