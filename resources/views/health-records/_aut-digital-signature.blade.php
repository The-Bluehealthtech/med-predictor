@php
    $readyProviders=$signatureProviders->where('status','ready');
    $history=$signatureRequests->get((int)$item->id, collect());
    $canSign=(int)$item->physician_id===(int)auth()->id() && auth()->user()->hasAnyRole(['doctor','team_doctor','club_medical','association_medical']);
@endphp
<div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
    <h3 class="font-semibold text-gray-900">Signature numérique de cette version PDF</h3>
    <p class="mt-1 text-sm text-gray-600">La signature certifie la version générée dans FIT. Elle ne remplace pas les signatures réglementaires du joueur et du médecin requises pour le dossier ADAMS.</p>

    @if($canSign)
        @if($readyProviders->isNotEmpty())
            <form method="POST" action="{{ route('medical-aut.digital-signature',[$healthRecord->id,$item->id]) }}" class="mt-3 flex flex-wrap items-end gap-3">
                @csrf
                <label class="text-sm">Fournisseur
                    <select name="provider" class="mt-1 rounded-lg border-gray-300" required>
                        @foreach($readyProviders as $provider)<option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>@endforeach
                    </select>
                </label>
                <button class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white">Signer numériquement</button>
            </form>
        @else
            <p class="mt-2 text-sm text-amber-700">Aucun fournisseur de signature n’est activé. Le workflow est prêt pour le Go Live.</p>
        @endif
    @else
        <p class="mt-2 text-sm text-gray-600">Seul le médecin enregistré sur cette demande peut déclencher la signature numérique.</p>
    @endif

    @if($history->isNotEmpty())
        <div class="mt-3 border-t border-gray-200 pt-3 text-sm">
            @foreach($history as $signature)
                <div class="flex flex-wrap items-center gap-2 py-1">
                    <span>{{ $signature->signer_name ?: 'Signataire' }} · {{ ucfirst($signature->status) }}</span>
                    @if($canSign && $signature->external_reference && in_array($signature->status,['pending','sent'],true))
                        <form method="POST" action="{{ route('medical-aut.digital-signature.sync',[$healthRecord->id,$item->id,$signature]) }}">@csrf<button class="font-semibold text-indigo-700">Synchroniser</button></form>
                    @endif
                    @if(data_get($signature->metadata,'signed_path'))
                        <a class="font-semibold text-emerald-700" href="{{ route('medical-aut.digital-signature.download',[$healthRecord->id,$item->id,$signature]) }}">PDF signé</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
