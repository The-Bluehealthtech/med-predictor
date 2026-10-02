{{-- Partie médicale de l'état de départ : saisie par le médecin du club, lue par les rôles médicaux uniquement.
     $medical vaut null quand l'utilisateur n'a pas accès : aucune donnée médicale n'est alors transmise à la vue. --}}
@php
    $fields = [
        'current_injuries' => 'Blessures ou pathologies en cours',
        'restrictions' => 'Restrictions et précautions',
        'treatments' => 'Traitements en cours',
        'aut' => 'Autorisation d\'usage à des fins thérapeutiques (AUT) en cours',
        'recommendations' => 'Recommandations médicales au staff national',
    ];
    $fitnessOptions = \App\Models\NationalSelectionReport::FITNESS;
    $cls = 'mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm';
@endphp
<div class="mt-5 rounded-lg border border-rose-200 bg-rose-50/40 p-4">
    <p class="text-xs font-semibold uppercase tracking-wider text-rose-800">🔒 Section médicale — réservée aux rôles médicaux</p>
    @if($medical === null)
        <p class="text-sm text-gray-600 mt-1">Son contenu n'est visible que par le médecin du club et le service médical de la fédération. Aptitude communiquée : <b>{{ $report?->fitnessLabel() ?? 'Non renseigné' }}</b>.</p>
    @elseif($editable)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
            <label class="block text-sm font-medium text-gray-700">Aptitude (visible par tous)
                <select name="fitness_status" class="{{ $cls }}"><option value="">Non renseigné</option>@foreach($fitnessOptions as $k => $l)<option value="{{ $k }}" @selected(($report?->fitness_status) === $k)>{{ $l }}</option>@endforeach</select>
            </label>
            @foreach($fields as $k => $l)
                <label class="block text-sm font-medium text-gray-700 {{ $k === 'recommendations' ? 'md:col-span-2' : '' }}">{{ $l }}
                    <textarea name="medical[{{ $k }}]" rows="2" maxlength="2000" class="{{ $cls }}">{{ $medical[$k] ?? '' }}</textarea>
                </label>
            @endforeach
        </div>
    @else
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm mt-3">
            @foreach($fields as $k => $l)
                <div><dt class="text-gray-500">{{ $l }}</dt><dd class="text-gray-900 whitespace-pre-line">{{ ($medical[$k] ?? '') !== '' ? $medical[$k] : '—' }}</dd></div>
            @endforeach
        </dl>
    @endif
</div>
