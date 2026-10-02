@extends('layouts.app')

@section('title', 'Approbation des licences - FIT Platform')

@php
    use App\Http\Controllers\Licensing\LicenseApprovalController as Approval;
    use App\Services\Licensing\{FifaIdRegistry, LicenseWorkflow};
    $holder = fn ($license) => app(LicenseWorkflow::class)->holderName($license);
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header
        title="Licences à examiner"
        subtitle="Traitez les demandes des clubs et prenez une décision lorsque le dossier est complet."
        eyebrow="Administration · fédération"
        :back-href="route('modules.index', ['section' => 'administration'])"
        back-label="Retour aux modules"
    />

    @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $key => $class)
        @if(session($key))<div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ $class }}" role="status">{{ session($key) }}</div>@endif
    @endforeach

    <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="h-review-now">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">À faire maintenant</p>
        <h2 id="h-review-now" class="mt-1 text-lg font-semibold text-slate-900">{{ $counts['pending'] }} demande(s) à examiner</h2>
        <p class="mt-1 text-sm text-slate-600">Ouvrez un dossier, vérifiez les éléments requis puis approuvez, demandez un complément ou refusez avec un motif.</p>
    </section>

    <div class="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border px-4 py-3 text-sm {{ $registryConnected ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-slate-200 bg-white text-slate-700' }}" data-fifa-id-status="{{ $registryConnected ? 'connected' : 'not_configured' }}">
        <span class="font-semibold">Registre d'identité FIFA ID :</span>
        <span>{{ $registryConnected ? 'connecté — la vérification est proposée dans chaque dossier.' : 'non connecté. L\'approbation reste possible ; la vérification d\'identité s\'activera dès que le registre sera configuré.' }}</span>
    </div>

    <nav class="mb-3 flex flex-wrap gap-2" aria-label="Filtrer par statut">
        @foreach(Approval::TABS as $key => $label)
            <a href="{{ route('licenses.validation', ['tab' => $key]) }}" @if($tab === $key) aria-current="page" @endif
               class="rounded-full px-3 py-1.5 text-sm font-semibold ring-1 {{ $tab === $key ? 'bg-slate-900 text-white ring-slate-900' : 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' }}">{{ $label }} · {{ number_format($counts[$key], 0, ',', ' ') }}</a>
        @endforeach
    </nav>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        @if($licenses->count() === 0)
            <p class="px-5 py-8 text-center text-sm text-slate-500">{{ $tab === 'pending' ? 'Aucune demande à examiner.' : 'Aucune demande dans cet onglet.' }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-2">Titulaire</th><th class="px-5 py-2">Club</th><th class="px-5 py-2">Licence</th><th class="px-5 py-2">Pièces</th><th class="px-5 py-2">Identité FIFA ID</th><th class="px-5 py-2">{{ $tab === 'pending' ? 'Reçue le' : 'Décision le' }}</th><th class="px-5 py-2 text-right">Dossier</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($licenses as $license)
                            <tr>
                                <td class="px-5 py-2 font-medium text-slate-900">{{ $holder($license) }}<span class="block text-xs font-normal text-slate-500">{{ $license->fifa_connect_id ? 'FIFA ' . $license->fifa_connect_id : 'sans identifiant FIFA' }}</span></td>
                                <td class="px-5 py-2">{{ $license->club?->name ?? '—' }}</td>
                                <td class="px-5 py-2">{{ LicenseWorkflow::describe($license) }}{{ $license->season ? ' · ' . $license->season : '' }}</td>
                                <td class="px-5 py-2 text-slate-600">{{ $license->documents_count }}</td>
                                <td class="px-5 py-2 text-slate-600">{{ $license->identity_check_status ? FifaIdRegistry::STATUSES[$license->identity_check_status] ?? $license->identity_check_status : 'Non vérifiée' }}</td>
                                <td class="px-5 py-2 text-slate-500">{{ ($tab === 'pending' ? $license->updated_at : $license->approved_at)?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-5 py-2 text-right"><a href="{{ route('licenses.review', $license) }}" class="rounded-lg {{ $tab === 'pending' ? 'bg-slate-900 text-white hover:bg-slate-800' : 'border border-slate-200 text-slate-700 hover:bg-slate-50' }} px-3 py-1.5 text-xs font-semibold">{{ $tab === 'pending' ? 'Examiner' : 'Voir' }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($licenses->hasPages())<div class="border-t border-slate-200 px-5 py-3">{{ $licenses->links() }}</div>@endif
        @endif
    </div>
</div>
@endsection
