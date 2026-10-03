@php($readyProviders = $documentSignatureProviders->where('status', 'ready'))
<section class="bg-white rounded-lg shadow p-5 space-y-3">
    <div>
        <h2 class="font-semibold text-gray-900">Signature numérique du passeport</h2>
        <p class="text-xs text-gray-500">PDF figé, empreinte SHA-256 et historique de signature. Un fournisseur est utilisable uniquement s'il est configuré et activé.</p>
    </div>
    @if($errors->has('signature'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $errors->first('signature') }}</div>
    @endif
    @if($canRequestDigitalSignature)
        <form method="POST" action="{{ $digitalSignatureAction }}" class="flex flex-wrap items-end gap-3">
            @csrf
            @if(isset($digitalSignaturePurpose))<input type="hidden" name="purpose" value="{{ $digitalSignaturePurpose }}">@endif
            <label class="block text-sm font-medium text-gray-700">Fournisseur
                <select name="provider" class="mt-1 block rounded-lg border-gray-300 text-sm" @disabled($readyProviders->isEmpty()) required>
                    @forelse($readyProviders as $provider)
                        <option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>
                    @empty
                        <option>Aucun fournisseur activé</option>
                    @endforelse
                </select>
            </label>
            <button @disabled($readyProviders->isEmpty()) class="px-4 py-2 rounded-lg text-sm font-semibold {{ $readyProviders->isEmpty() ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-slate-800 text-white hover:bg-slate-900' }}">Signer numériquement</button>
        </form>
        @if($readyProviders->isEmpty())
            <p class="text-sm text-amber-700">Prêt pour le Go Live : activez un fournisseur dans Configuration des API pour rendre cette action disponible.</p>
        @endif
    @else
        <p class="text-sm text-gray-600">{{ $digitalSignatureBlockedMessage }}</p>
    @endif
    <div class="border-t border-gray-100 pt-3">
        <h3 class="text-sm font-semibold text-gray-800">Historique</h3>
        @forelse($documentSignatureRequests as $signatureRequest)
            <div class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-sm">
                <div class="flex flex-wrap justify-between gap-2">
                    <span>{{ data_get($signatureRequest->metadata, 'provider_name', $signatureRequest->provider) }} · {{ $signatureRequest->signer_name }}</span>
                    <span>{{ ucfirst($signatureRequest->status) }} · {{ optional($signatureRequest->requested_at)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="mt-1 flex gap-3">
                    @if(($canManageDigitalSignature ?? false) && $signatureRequest->external_reference && in_array($signatureRequest->status, ['pending','sent'], true))
                        <form method="POST" action="{{ route('passports.digital-signature.sync', [$passportPlayerId, $signatureRequest]) }}">@csrf<button class="text-blue-700 font-semibold">Synchroniser</button></form>
                    @endif
                    @if(data_get($signatureRequest->metadata, 'signed_path'))
                        <a class="text-emerald-700 font-semibold" href="{{ route('passports.digital-signature.download', [$passportPlayerId, $signatureRequest]) }}">PDF signé</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="mt-2 text-sm text-gray-500">Aucune signature numérique pour ce passeport.</p>
        @endforelse
    </div>
</section>
