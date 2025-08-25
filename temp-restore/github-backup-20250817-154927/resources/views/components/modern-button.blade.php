@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, warning
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'class' => ''
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none';
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base'
    ];
    
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent text-white hover:from-blue-600 hover:to-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gradient-to-r from-gray-500 to-gray-600 border border-transparent text-white hover:from-gray-600 hover:to-gray-700 focus:ring-gray-500',
        'success' => 'bg-gradient-to-r from-green-500 to-green-600 border border-transparent text-white hover:from-green-600 hover:to-green-700 focus:ring-green-500',
        'danger' => 'bg-gradient-to-r from-red-500 to-red-600 border border-transparent text-white hover:from-red-600 hover:to-red-700 focus:ring-red-500',
        'warning' => 'bg-gradient-to-r from-yellow-500 to-yellow-600 border border-transparent text-white hover:from-yellow-600 hover:to-yellow-700 focus:ring-yellow-500',
        'purple' => 'bg-gradient-to-r from-purple-500 to-purple-600 border border-transparent text-white hover:from-purple-600 hover:to-purple-700 focus:ring-purple-500'
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant] . ' ' . $class;
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button> 