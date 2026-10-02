{{-- Icônes au trait (24×24) des modules FIT. $name : clé d'icône ; $class : classes de taille et de couleur.
     Une clé inconnue est affichée telle quelle (anciens catalogues). --}}
@php
    $paths = [
        'stethoscope' => '<path d="M6 3v6a4 4 0 0 0 8 0V3"/><path d="M10 13v2a5 5 0 0 0 10 0v-2"/><circle cx="20" cy="11" r="2"/>',
        'pulse' => '<path d="M3 12h4l2-6 4 12 2-6h6"/>',
        'folder' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 10.5v5M9.5 13h5"/>',
        'clipboard-check' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9z"/><path d="m9 13 2 2 4-4"/>',
        'clipboard-list' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 3h6v3H9z"/><path d="M9 11h6M9 15h4"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/>',
        'gauge' => '<path d="M3.5 18a9 9 0 1 1 17 0"/><path d="m12 14 4-4"/>',
        'pencil' => '<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="m13 7 4 4"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
        'watch' => '<rect x="7" y="6" width="10" height="12" rx="2"/><path d="m9 6 1-3h4l1 3M9 18l1 3h4l1-3"/>',
        'flag' => '<path d="M5 21V4"/><path d="M5 4h12l-2 4 2 4H5"/>',
        'id-card' => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.6-1.4 1.7-2 3-2s2.4.6 3 2M14 10h4M14 14h3"/>',
        'key' => '<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8M16 7l2 2M14 9l2 2"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'building' => '<path d="M4 21V8l8-5 8 5v13"/><path d="M9 21v-6h6v6"/>',
        'landmark' => '<path d="M3 21h18M5 18v-7M10 18v-7M14 18v-7M19 18v-7M2 9l10-6 10 6z"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 5a3 3 0 0 1 0 6M18 14c2 .7 3 2.7 3 6"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/>',
        'trophy' => '<path d="M8 4h8v5a4 4 0 0 1-8 0z"/><path d="M8 6H5a3 3 0 0 0 3 4M16 6h3a3 3 0 0 1-3 4M12 13v4M8 21h8M10 17h4"/>',
        'whistle' => '<circle cx="9" cy="14" r="5"/><path d="M12.5 10.5 21 6v4l-6 2"/><path d="M4 9 2 7"/>',
        'badge' => '<rect x="4" y="3" width="16" height="18" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M8 17c.8-1.6 2.3-2.5 4-2.5s3.2.9 4 2.5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>',
        'transfer' => '<path d="M4 8h14l-4-4M20 16H6l4 4"/>',
        'banknote' => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10v4M18 10v4"/>',
        'sliders' => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
        'file' => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>',
        'chip' => '<rect x="7" y="7" width="10" height="10" rx="1.5"/><path d="M10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/>',
        'shirt' => '<path d="M8 3 3 6l2 5 3-1v11h8V10l3 1 2-5-5-3a4 4 0 0 1-8 0z"/>',
    ];
    $iconName = $name ?? '';
@endphp
@if(isset($paths[$iconName]))
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $class ?? 'w-5 h-5' }}" aria-hidden="true">{!! $paths[$iconName] !!}</svg>
@else
<span class="{{ $class ?? '' }}" aria-hidden="true">{{ $iconName }}</span>
@endif
