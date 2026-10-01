@extends('layouts.secretary')

@section('title', 'Secrétariat médical')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <div class="text-xs uppercase tracking-[0.14em] font-semibold text-blue-700">Parcours patient</div>
            <h1 class="text-3xl font-bold text-slate-950 mt-2">Secrétariat médical</h1>
            <p class="text-slate-600 mt-2 max-w-2xl">Organiser l’arrivée du joueur, préparer son dossier et assurer le passage de relais au médecin.</p>
        </div>
        <button type="button" onclick="document.getElementById('new-appointment-panel').classList.toggle('hidden')"
                class="px-4 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
            + Nouveau rendez-vous
        </button>
    </header>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm">{{ session('success') }}</div>
    @endif

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border rounded-2xl p-4">
            <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Aujourd’hui</div>
            <div class="mt-1 text-2xl font-bold text-slate-950">{{ $stats['today'] }}</div>
            <div class="text-sm text-slate-500">rendez-vous</div>
        </div>
        <div class="bg-white border rounded-2xl p-4">
            <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Salle d’attente</div>
            <div class="mt-1 text-2xl font-bold text-amber-700">{{ $stats['waiting'] }}</div>
            <div class="text-sm text-slate-500">joueur(s)</div>
        </div>
        <div class="bg-white border rounded-2xl p-4">
            <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Chez le médecin</div>
            <div class="mt-1 text-2xl font-bold text-blue-700">{{ $stats['in_consultation'] }}</div>
            <div class="text-sm text-slate-500">consultation(s)</div>
        </div>
        <div class="bg-white border rounded-2xl p-4">
            <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Documents</div>
            <div class="mt-1 text-2xl font-bold text-slate-950">{{ $stats['documents_pending'] }}</div>
            <div class="text-sm text-slate-500">à intégrer</div>
        </div>
    </section>

    <section id="new-appointment-panel" class="{{ $sourceVisit ? '' : 'hidden' }} bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-slate-900">Planifier un rendez-vous médical</h2>
            <p class="text-sm text-slate-500 mt-1">Le motif du rendez-vous oriente le parcours, sans créer encore d’acte médical.</p>
        </div>
        <form method="POST" action="{{ route('secretary.appointments.store') }}" class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @csrf
            @if($sourceVisit)<input type="hidden" name="source_visit_id" value="{{ $sourceVisit->id }}">@endif
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Joueur</label>
                <select name="athlete_id" required class="w-full rounded-xl border-slate-300">
                    <option value="">Sélectionner</option>
                    @foreach($athletes as $athlete)
                        <option value="{{ $athlete->id }}" @selected((int)old('athlete_id', $sourceVisit?->athlete_id) === (int)$athlete->id)>{{ $athlete->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Date</label>
                <input type="date" name="appointment_date" required class="w-full rounded-xl border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Heure</label>
                <input type="time" name="appointment_time" required class="w-full rounded-xl border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Objet</label>
                <select name="appointment_type" required class="w-full rounded-xl border-slate-300">
                    <option value="consultation">Consultation</option>
                    <option value="follow_up" @selected($sourceVisit)>Suivi</option>
                    <option value="routine_checkup">Contrôle médical</option>
                    <option value="injury_assessment">Évaluation de blessure</option>
                    <option value="cardiac_evaluation">Évaluation cardiaque</option>
                    <option value="concussion_assessment">Commotion / SCAT</option>
                    <option value="emergency">Urgence</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Motif administratif</label>
                <input name="reason" value="{{ old('reason', $sourceVisit ? 'Actes prescrits : '.implode(', ', (array)data_get($sourceVisit->administrative_data, 'prescribed_modules', [])) : '') }}" class="w-full rounded-xl border-slate-300" placeholder="Ex. douleur genou, contrôle pré-compétition, suivi…">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Médecin</label>
                <select name="doctor_id" class="w-full rounded-xl border-slate-300">
                    <option value="">À attribuer</option>
                    @foreach($doctors as $doctor)<option value="{{ $doctor->id }}">{{ $doctor->name }}</option>@endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold">Enregistrer le rendez-vous</button>
            </div>
        </form>
    </section>

    @if($pendingOrders->count())
    <section class="bg-white border border-violet-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-violet-100 bg-violet-50/60">
            <div class="text-xs uppercase tracking-wide font-semibold text-violet-700">À organiser</div>
            <h2 class="font-semibold text-slate-950 mt-1">Actes prescrits par les médecins</h2>
            <p class="text-sm text-slate-600 mt-1">Ces demandes proviennent de consultations terminées et nécessitent une organisation ou un rendez-vous.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($pendingOrders as $order)
                @php
                    $orderedPlayer = $order->athlete?->player;
                    $modules = (array)data_get($order->administrative_data, 'prescribed_modules', []);
                    $labels = [
                        'pcma'=>'PCMA','fmarc'=>'F-MARC / blessure','scat'=>'SCAT / commotion',
                        'imaging'=>'Imagerie','mri'=>'IRM','mapa'=>'MAPA','ecg_effort'=>'ECG d’effort',
                        'laboratory'=>'Laboratoire','dental'=>'Dentaire','postural'=>'Posture',
                        'specialist'=>'Avis spécialiste','physiotherapy'=>'Kinésithérapie',
                    ];
                @endphp
                <div class="p-5 grid grid-cols-1 lg:grid-cols-[1fr_180px_1.4fr_auto] gap-4 lg:items-center">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $orderedPlayer?->full_name ?? $order->athlete?->name ?? 'Joueur' }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $orderedPlayer?->club?->name ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">Consultation</div>
                        <div class="text-sm text-slate-700 mt-1">{{ $order->visit_date?->format('d/m/Y') }}</div>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($modules as $module)
                            <span class="px-2 py-1 rounded-md bg-violet-50 text-violet-800 text-xs font-medium">{{ $labels[$module] ?? $module }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('secretary.dashboard', ['source_visit'=>$order->id]) }}"
                       class="px-3.5 py-2 rounded-lg bg-violet-600 text-white text-sm font-semibold text-center hover:bg-violet-700">
                        Programmer
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-slate-950">Flux des patients</h2>
            <p class="text-sm text-slate-500 mt-1">Le statut indique l’étape réelle du parcours de soins.</p>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentAppointments as $appointment)
                @php
                    $player = $appointment->athlete?->player;
                    $status = $appointment->status;
                    $badge = match($status) {
                        'Enregistré' => 'bg-amber-50 text-amber-800',
                        'En cours' => 'bg-blue-50 text-blue-800',
                        'Terminé' => 'bg-emerald-50 text-emerald-800',
                        'Annulé','No-show' => 'bg-red-50 text-red-700',
                        default => 'bg-slate-100 text-slate-700',
                    };
                @endphp
                <div class="p-5 grid grid-cols-1 lg:grid-cols-[minmax(220px,1fr)_180px_170px_1fr_auto] gap-4 lg:items-center">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $player?->full_name ?? $appointment->athlete?->name ?? 'Joueur' }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $player?->club?->name ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">Rendez-vous</div>
                        <div class="text-sm text-slate-800 mt-1">{{ $appointment->appointment_date?->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400">Objet</div>
                        <div class="text-sm text-slate-800 mt-1">{{ $appointment->type_label }}</div>
                    </div>
                    <div>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $appointment->status_label }}</span>
                        @if($appointment->reason)<div class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $appointment->reason }}</div>@endif
                    </div>
                    <div class="flex flex-wrap gap-2 lg:justify-end">
                        @if(in_array($status, ['Planifié','Confirmé']))
                            <a href="{{ route('secretary.appointments.intake', $appointment) }}"
                               class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">Pré-accueil</a>
                        @elseif($status === 'Enregistré')
                            <a href="{{ route('secretary.appointments.intake', $appointment) }}"
                               class="px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700">Documents</a>
                            <span class="px-3 py-2 rounded-lg bg-amber-50 text-amber-800 text-sm font-semibold">En attente médecin</span>
                        @elseif($status === 'En cours')
                            <span class="px-3 py-2 rounded-lg bg-blue-50 text-blue-800 text-sm font-semibold">Consultation en cours</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-500">Aucun rendez-vous médical à venir.</div>
            @endforelse
        </div>
    </section>

    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-slate-950">Documents récemment collectés</h2>
            <p class="text-sm text-slate-500 mt-1">Pièces fournies au secrétariat pour être disponibles pendant la prise en charge.</p>
        </div>
        <div class="divide-y">
            @forelse($recentDocuments as $document)
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="text-sm font-medium text-slate-900">{{ $document->file_name }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $document->visit?->athlete?->player?->full_name ?? $document->visit?->athlete?->name ?? 'Joueur' }} · {{ $document->document_type_label }}</div>
                    </div>
                    <span class="text-xs text-slate-400">{{ $document->created_at?->format('d/m/Y H:i') }}</span>
                </div>
            @empty
                <div class="p-6 text-sm text-slate-500">Aucun document collecté.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
