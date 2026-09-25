@props([
    'nationality' => null,
    'association' => null,
    'showNationalityFlag' => true,
    'showAssociationLogo' => true
])

@php
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

<div class="flex items-center space-x-2">
    @if($showNationalityFlag && $nationality)
        <img 
            src="https://flagcdn.com/w40/{{ $countryCode }}.png"
            alt="Drapeau {{ $nationality }}"
            class="w-5 h-4 rounded shadow-sm"
            title="{{ $nationality }}"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
        />
        <div class="text-xs text-gray-600 font-medium" style="display: none;">
            {{ strtoupper($countryCode) }}
        </div>
    @endif
    
    @if($showAssociationLogo && $association)
        @if($association->name && str_contains(strtolower($association->name), 'ftf'))
            {{-- Logo FTF --}}
            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm" title="FTF">
                FTF
            </div>
        @else
            {{-- Logo générique pour autres associations --}}
            <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm" title="{{ $association->name ?? 'Association' }}">
                {{ strtoupper(substr($association->name ?? 'ASSOC', 0, 3)) }}
            </div>
        @endif
    @endif
</div>







