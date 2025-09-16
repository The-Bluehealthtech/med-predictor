@props([
    'nationality' => null,
    'association' => null,
    'showNationalityFlag' => true,
    'showAssociationLogo' => true,
    'size' => 'medium' // small, medium, large
])

@php
    // Définir les tailles
    $flagSizes = [
        'small' => 'w-6 h-4',
        'medium' => 'w-8 h-6', 
        'large' => 'w-12 h-8'
    ];
    
    $logoSizes = [
        'small' => 'w-8 h-8',
        'medium' => 'w-12 h-12',
        'large' => 'w-16 h-16'
    ];
    
    $flagSize = $flagSizes[$size] ?? $flagSizes['medium'];
    $logoSize = $logoSizes[$size] ?? $logoSizes['medium'];
    
    // Mapper les nationalités aux codes de pays ISO
    $countryCodes = [
        'Tunisie' => 'tn',
        'Maroc' => 'ma',
        'Algérie' => 'dz',
        'Mali' => 'ml',
        'Sénégal' => 'sn',
        'Côte d\'Ivoire' => 'ci',
        'Nigeria' => 'ng',
        'Portugal' => 'pt',
        'Norway' => 'no',
        'France' => 'fr',
        'Argentina' => 'ar'
    ];
    
    $countryCode = $countryCodes[$nationality] ?? 'un';
@endphp

<div class="flex items-center space-x-3">
    @if($showNationalityFlag && $nationality)
        <div class="flex flex-col items-center">
            <img 
                src="https://flagcdn.com/w80/{{ $countryCode }}.png"
                alt="Drapeau {{ $nationality }}"
                class="{{ $flagSize }} rounded shadow-sm"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
            />
            <div class="text-xs text-center text-gray-600 font-medium" style="display: none;">
                {{ strtoupper($countryCode) }}
            </div>
            <span class="text-xs text-gray-500 mt-1">{{ $nationality }}</span>
        </div>
    @endif
    
    @if($showAssociationLogo && $association)
        <div class="flex flex-col items-center">
            @if($association->name && str_contains(strtolower($association->name), 'ftf'))
                {{-- Logo FTF --}}
                <div class="{{ $logoSize }} bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg">
                    FTF
                </div>
            @else
                {{-- Logo générique pour autres associations --}}
                <div class="{{ $logoSize }} bg-gray-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-lg">
                    {{ strtoupper(substr($association->name ?? 'ASSOC', 0, 3)) }}
                </div>
            @endif
            <span class="text-xs text-gray-500 mt-1">{{ $association->name ?? 'Association' }}</span>
        </div>
    @endif
</div>







