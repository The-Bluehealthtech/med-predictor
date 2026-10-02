{{-- Carte compacte d'un module dans une étape de parcours. $item : entrée du catalogue ; $iconTone : teintes par couleur. --}}
@php
    $cardName = app()->getLocale() === 'en' ? (trans('modules_fit.names')[$item['name']] ?? $item['name']) : $item['name'];
    $cardDesc = app()->getLocale() === 'en' ? (trans('modules_fit.descriptions')[$item['description']] ?? $item['description']) : $item['description'];
@endphp
<div class="module-card group flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-3 cursor-pointer transition hover:border-blue-300 hover:shadow-sm"
     data-search="{{ e(mb_strtolower($cardName.' '.$cardDesc.' '.($item['category'] ?? '').' '.($item['group'] ?? ''))) }}"
     onclick="handleModuleClick('{{ $item['route'] }}', '{{ addslashes($cardName) }}', event)">
    <span class="inline-flex shrink-0 items-center justify-center w-9 h-9 rounded-lg {{ $iconTone[$item['color'] ?? 'gray'] ?? 'bg-slate-100 text-slate-600' }}">@include('modules.partials.icon', ['name' => $item['icon'] ?? '', 'class' => 'w-5 h-5'])</span>
    <span class="min-w-0">
        <span class="block text-sm font-semibold text-gray-900 group-hover:text-blue-700">{{ $cardName }}</span>
        <span class="block text-xs text-gray-500 line-clamp-2">{{ $cardDesc }}</span>
    </span>
</div>
