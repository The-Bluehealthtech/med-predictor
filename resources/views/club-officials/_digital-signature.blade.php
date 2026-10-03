@php($readyProviders=$signatureProviders->where('status','ready'))
<div class="bg-white rounded-lg shadow p-5 space-y-3">
    <div>
        <h2 class="font-semibold text-gray-900">Certification numérique de la fiche</h2>
        <p class="text-sm text-gray-600">Cette signature certifie l’instantané FIT de la fiche. Elle ne vaut pas enregistrement officiel dans FIFA Connect.</p>
    </div>
    @if(session('signature_error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ session('signature_error') }}</div>
    @endif
    @if($canSign)
        @if($readyProviders->isNotEmpty())
            <form method="POST" action="{{ route('club-officials.digital-signature',[$club,$official]) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <label class="text-sm">Fournisseur
                    <select name="provider" class="mt-1 rounded-lg border-gray-300" required>
                        @foreach($readyProviders as $provider)<option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>@endforeach
                    </select>
                </label>
                <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Signer cette version</button>
            </form>
        @else
            <p class="text-sm text-amber-700">Aucun fournisseur de signature n’est activé. Le workflow est prêt pour le Go Live.</p>
        @endif
    @else
        <p class="text-sm text-gray-600">La signature est réservée à l’administrateur du club ou de la fédération dans son périmètre.</p>
    @endif

    <div class="border-t border-gray-100 pt-3">
        <h3 class="text-sm font-semibold text-gray-800">Historique</h3>
        @forelse($signatureRequests as $signature)
            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm">
                <span>{{ $signature->signer_name ?: 'Signataire' }} · {{ $signature->signer_role }} · {{ ucfirst($signature->status) }}</span>
                <span class="flex gap-3">
                    @if($canSign && $signature->external_reference && in_array($signature->status,['pending','sent'],true))
                        <form method="POST" action="{{ route('club-officials.digital-signature.sync',[$club,$official,$signature]) }}">@csrf<button class="font-semibold text-blue-700">Synchroniser</button></form>
                    @endif
                    @if(data_get($signature->metadata,'signed_path'))
                        <a class="font-semibold text-emerald-700" href="{{ route('club-officials.digital-signature.download',[$club,$official,$signature]) }}">PDF signé</a>
                    @endif
                </span>
            </div>
        @empty
            <p class="mt-2 text-sm text-gray-500">Aucune signature numérique pour cette fiche.</p>
        @endforelse
    </div>
</div>
