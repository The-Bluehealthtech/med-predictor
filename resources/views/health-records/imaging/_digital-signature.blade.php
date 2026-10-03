@php($readyProviders = $documentSignatureProviders->where('status', 'ready'))
<details class="fi-pacs">
    <summary>Signature numérique du PDF
        <span class="fi-badge">{{ $documentSignatureRequests->first()?->status ? ucfirst($documentSignatureRequests->first()->status) : 'Non signée' }}</span>
    </summary>
    <p>La signature s’applique uniquement à cette version validée du compte rendu PDF. Le DICOM SR reste un export distinct.</p>

    @if($canSignReport)
        @if($readyProviders->isNotEmpty())
            <form method="POST" action="{{ route('medical-imaging.report.digital-signature',[$healthRecord,$study,$report]) }}">
                @csrf
                <label>Fournisseur
                    <select name="provider" required>
                        @foreach($readyProviders as $provider)
                            <option value="{{ $provider['slug'] }}">{{ $provider['name'] }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="fi-button">Signer numériquement le PDF</button>
            </form>
        @else
            <p class="fi-notice">Aucun fournisseur de signature n’est activé. Le workflow est prêt pour le Go Live.</p>
        @endif
    @else
        <p class="fi-notice">La signature numérique est réservée à un médecin autorisé.</p>
    @endif

    @if($documentSignatureRequests->isNotEmpty())
        <div class="fi-history">
            @foreach($documentSignatureRequests as $signature)
                <p>
                    {{ $signature->signer_name ?: 'Signataire' }} · {{ ucfirst($signature->status) }}
                    @if($signature->requested_at) · {{ $signature->requested_at->format('d/m/Y H:i') }} @endif
                    @if($canSignReport && $signature->external_reference && in_array($signature->status,['pending','sent'],true))
                        · <form style="display:inline" method="POST" action="{{ route('medical-imaging.report.digital-signature.sync',[$healthRecord,$study,$report,$signature]) }}">@csrf<button class="fi-link" type="submit">Synchroniser</button></form>
                    @endif
                    @if(data_get($signature->metadata,'signed_path'))
                        · <a href="{{ route('medical-imaging.report.digital-signature.download',[$healthRecord,$study,$report,$signature]) }}">PDF signé</a>
                    @endif
                </p>
            @endforeach
        </div>
    @endif
</details>
