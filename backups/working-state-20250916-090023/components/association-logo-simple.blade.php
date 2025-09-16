@props(['size' => 'md'])

@php
    $sizeClasses = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        '2xl' => 'w-20 h-20'
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="association-logo-simple {{ $sizeClass }} bg-green-500 rounded flex items-center justify-center text-white font-bold">
    🏆
</div>

