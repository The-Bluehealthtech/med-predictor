@props(['class' => '', 'hover' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden ' . ($hover ? 'hover:shadow-xl transition-all duration-300' : '') . ' ' . $class]) }}>
    {{ $slot }}
</div> 