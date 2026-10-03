@php($readyProviders=$signatureProviders->where('status','ready'))
<div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 no-print space-y-3">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-semibold text-slate-900">Certification numérique de la carte</h2>
            <p class="mt-1 text-sm text-slate-600">La signature porte sur cette version approuvée de la carte CR80. Elle ne crée ni ne remplace un identifiant FIFA Connect.</p>
        </div>
        <a href="{{ route('licenses.card.pdf',$license) }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">PDF CR80</a>
    </div>
    @if(session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ session('error') }}</div>@endif
    @if($canSignCard)
        @if($readyProviders->isNotEmpty())
            <form method="POST" action="{{ route('licenses.card.digital-signature',$license) }}" class="flex flex-wrap items-end gap-3">@csrf
                <label class="text-sm">Fournisseur
                    <select name="provider" class="mt-1 rounded-lg border-gray-300" required>
                        @foreach($readyProviders as $provider)<option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>@endforeach
                    </select>
                </label>
                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Signer cette version</button>
            </form>
        @else
            <p class="text-sm text-amber-700">Aucun fournisseur de signature n’est activé. Le workflow est prêt pour le Go Live.</p>
        @endif
    @else
        <p class="text-sm text-slate-600">La signature métier est réservée à un administrateur ou registraire de la fédération.</p>
    @endif

    <div class="border-t border-slate-100 pt-3">
        <h3 class="text-sm font-semibold text-slate-800">Historique</h3>
        @forelse($signatureRequests as $signature)
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-lg bg-slate-50 px-3 py-2 text-sm">
                <span>{{ $signature->signer_name ?: 'Signataire' }} · {{ $signature->signer_role }} · {{ ucfirst($signature->status) }}</span>
                <span class="flex gap-3">
                    @if($canSignCard && $signature->external_reference && in_array($signature->status,['pending','sent'],true))
                        <form method="POST" action="{{ route('licenses.card.digital-signature.sync',[$license,$signature]) }}">@csrf<button class="font-semibold text-blue-700">Synchroniser</button></form>
                    @endif
                    @if(data_get($signature->metadata,'signed_path'))
                        <a class="font-semibold text-emerald-700" href="{{ route('licenses.card.digital-signature.download',[$license,$signature]) }}">PDF signé</a>
                    @endif
                </span>
            </div>
        @empty
            <p class="mt-2 text-sm text-slate-500">Aucune signature numérique pour cette carte.</p>
        @endforelse
    </div>
</div>
