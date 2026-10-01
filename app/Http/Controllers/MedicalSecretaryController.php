<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Athlete;
use App\Models\Document;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

final class MedicalSecretaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            abort_unless($user, 401);

            if (($user->role ?? null) !== 'secretary') {
                app(MedicalRecordAccess::class)->authorizeRole($user);
            }

            return $next($request);
        });
    }

    private function playersQuery()
    {
        $user = auth()->user();

        if (($user->role ?? null) !== 'secretary') {
            return app(MedicalRecordAccess::class)->scopePlayers($user, Player::query());
        }

        $query = Player::query();
        if ($user->club_id) {
            return $query->where('club_id', $user->club_id);
        }
        if ($user->association_id) {
            return $query->whereHas('club', fn ($club) => $club->where('association_id', $user->association_id));
        }

        abort(403);
    }

    private function authorizePlayer(Player $player): void
    {
        $user = auth()->user();

        if (($user->role ?? null) !== 'secretary') {
            app(MedicalRecordAccess::class)->authorize($user, $player, null);
            return;
        }

        $allowed = $this->playersQuery()->whereKey($player->id)->exists();
        abort_unless($allowed, 403);
    }

    private function athletesQuery()
    {
        return Athlete::query()->whereIn('player_id', $this->playersQuery()->select('players.id'));
    }

    public function dashboard(): View
    {
        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();

        $allowedPlayerIds = $this->playersQuery()->select('players.id');

        $appointments = Appointment::with(['athlete.player.club', 'doctor', 'visit.documents'])
            ->whereHas('athlete', fn ($athlete) => $athlete->whereIn('player_id', $allowedPlayerIds))
            ->where('appointment_date', '>=', $today)
            ->orderBy('appointment_date')
            ->limit(40)
            ->get();

        $recentAppointments = $appointments->take(20);
        $recentDocuments = Document::with(['visit.athlete.player', 'uploadedBy'])
            ->whereHas('visit.athlete', fn ($athlete) => $athlete->whereIn('player_id', $this->playersQuery()->select('players.id')))
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'today' => $appointments->filter(fn ($a) => $a->appointment_date?->between($today, $tomorrow, false))->count(),
            'waiting' => $appointments->where('status', 'Enregistré')->count(),
            'in_consultation' => $appointments->where('status', 'En cours')->count(),
            'documents_pending' => $recentDocuments->where('status', 'pending')->count(),
        ];

        $pendingOrders = Visit::with(['athlete.player.club', 'doctor'])
            ->whereHas('athlete', fn ($athlete) => $athlete->whereIn('player_id', $this->playersQuery()->select('players.id')))
            ->where('status', 'Terminé')
            ->orderByDesc('visit_date')
            ->limit(50)
            ->get()
            ->filter(fn ($visit) =>
                data_get($visit->administrative_data, 'orders_status') === 'pending'
                && count((array) data_get($visit->administrative_data, 'prescribed_modules', [])) > 0
            )
            ->values();

        $sourceVisit = null;
        if (request()->filled('source_visit')) {
            $sourceVisit = $pendingOrders->firstWhere('id', (int) request('source_visit'));
            abort_unless($sourceVisit, 404);
        }

        $athletes = $this->athletesQuery()->with('player')->orderBy('name')->limit(1000)->get();
        $doctors = User::query()
            ->whereIn('role', ['club_medical', 'association_medical', 'doctor'])
            ->orderBy('name')
            ->get();

        return view('secretary.dashboard', compact(
            'stats',
            'recentAppointments',
            'recentDocuments',
            'athletes',
            'doctors',
            'pendingOrders',
            'sourceVisit'
        ));
    }

    public function storeAppointment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'appointment_type' => 'required|in:consultation,follow_up,emergency,pre_season,post_match,rehabilitation,routine_checkup,injury_assessment,cardiac_evaluation,concussion_assessment',
            'doctor_id' => 'nullable|exists:users,id',
            'doctor_name' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:4000',
            'source_visit_id' => 'nullable|exists:visits,id',
        ]);

        $athlete = $this->athletesQuery()->with('player')->findOrFail($validated['athlete_id']);
        $appointmentAt = Carbon::parse($validated['appointment_date'].' '.$validated['appointment_time']);

        $appointment = Appointment::create([
            'athlete_id' => $athlete->id,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'created_by' => auth()->id(),
            'appointment_date' => $appointmentAt,
            'duration_minutes' => 30,
            'appointment_type' => $validated['appointment_type'],
            'status' => 'Planifié',
            'reason' => $validated['reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'reminder_settings' => [
                'doctor_name' => $validated['doctor_name'] ?? null,
                'player_id' => $athlete->player_id,
            ],
        ]);

        if (!empty($validated['source_visit_id'])) {
            $sourceVisit = Visit::with('athlete.player')->findOrFail($validated['source_visit_id']);
            abort_unless($sourceVisit->athlete?->player, 422);
            $this->authorizePlayer($sourceVisit->athlete->player);
            abort_unless((int) $sourceVisit->athlete_id === (int) $athlete->id, 422, 'Le rendez-vous doit concerner le joueur de la prescription.');

            $admin = $sourceVisit->administrative_data ?? [];
            $admin['orders_status'] = 'scheduled';
            $admin['follow_up_appointment_id'] = $appointment->id;
            $admin['orders_scheduled_at'] = now()->toIso8601String();
            $admin['orders_scheduled_by'] = auth()->id();
            $sourceVisit->update(['administrative_data' => $admin]);
        }

        return redirect()->route('secretary.dashboard')->with('success', 'Rendez-vous médical enregistré.');
    }

    public function intake(Appointment $appointment): View
    {
        $appointment->load(['athlete.player.club', 'doctor', 'visit.documents']);
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');
        $this->authorizePlayer($appointment->athlete->player);

        $player = $appointment->athlete->player;
        $dossier = $player->baseHealthRecord()->first();

        return view('secretary.intake', compact('appointment', 'player', 'dossier'));
    }

    public function checkIn(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->load('athlete.player');
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');
        $this->authorizePlayer($appointment->athlete->player);

        $validated = $request->validate([
            'reason_confirmed' => 'nullable|string|max:1000',
            'symptoms_summary' => 'nullable|string|max:2000',
            'patient_reported_allergies' => 'nullable|string|max:2000',
            'patient_reported_medications' => 'nullable|string|max:2000',
            'secretary_notes' => 'nullable|string|max:2000',
        ]);

        $player = $appointment->athlete->player;
        $dossier = $player->baseHealthRecord()->first();

        DB::transaction(function () use ($appointment, $validated, $player, $dossier) {
            $visit = Visit::firstOrNew(['appointment_id' => $appointment->id]);
            $visit->fill([
                'athlete_id' => $appointment->athlete_id,
                'doctor_id' => $appointment->doctor_id,
                'visit_date' => $appointment->appointment_date,
                'visit_type' => $appointment->appointment_type ?: 'consultation',
                'status' => 'Enregistré',
                'notes' => $validated['secretary_notes'] ?? null,
                'administrative_data' => [
                    'checked_in_at' => now()->toIso8601String(),
                    'checked_in_by' => auth()->id(),
                    'dossier_state' => $dossier ? 'existing' : 'to_initialize',
                    'base_health_record_id' => $dossier?->id,
                    'player_id' => $player->id,
                    'pre_intake' => [
                        'reason_confirmed' => $validated['reason_confirmed'] ?? null,
                        'symptoms_summary' => $validated['symptoms_summary'] ?? null,
                        'patient_reported_allergies' => $validated['patient_reported_allergies'] ?? null,
                        'patient_reported_medications' => $validated['patient_reported_medications'] ?? null,
                    ],
                ],
            ]);
            $visit->save();

            $settings = $appointment->reminder_settings ?? [];
            $settings['checked_in_at'] = now()->toIso8601String();
            $settings['dossier_state'] = $dossier ? 'existing' : 'to_initialize';
            $settings['visit_id'] = $visit->id;

            $appointment->update([
                'status' => 'Enregistré',
                'reminder_settings' => $settings,
            ]);
        });

        return redirect()->route('secretary.dashboard')
            ->with('success', 'Pré-accueil terminé. Le joueur est placé en salle d’attente médicale.');
    }

    public function receive(Appointment $appointment): RedirectResponse
    {
        $appointment->load(['athlete.player', 'visit']);
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');
        $this->authorizePlayer($appointment->athlete->player);

        $visit = $appointment->visit;
        abort_unless($visit, 422, 'Le pré-accueil doit être terminé avant la consultation.');

        DB::transaction(function () use ($appointment, $visit) {
            $visit->update(['status' => 'En cours']);
            $appointment->update(['status' => 'En cours']);
        });

        $player = $appointment->athlete->player;
        $dossier = $player->baseHealthRecord()->first();

        if ($dossier) {
            return redirect()->route('health-records.show', [
                'healthRecord' => $dossier,
                'appointment_id' => $appointment->id,
                'visit_id' => $visit->id,
                'consultation' => 1,
            ])->with('success', 'Le joueur est maintenant en consultation.');
        }

        return redirect()->route('health-records.create', [
            'player_id' => $player->id,
            'appointment_id' => $appointment->id,
            'visit_id' => $visit->id,
        ])->with('success', 'Première consultation : initialisez le dossier médical du joueur.');
    }

    public function uploadDocument(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->load(['athlete.player', 'visit']);
        abort_unless($appointment->visit, 422, 'Le joueur doit être enregistré à l’accueil avant le dépôt de documents.');

        $validated = $request->validate([
            'document_type' => 'required|in:medical_report,lab_result,radiology,prescription,consent_form,insurance_form,referral,discharge_summary,progress_note,other',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,dcm|max:10240',
            'description' => 'nullable|string|max:2000',
        ]);

        $file = $request->file('document_file');
        $path = $file->store('medical-intake/'.$appointment->visit->id, 'local');

        Document::create([
            'visit_id' => $appointment->visit->id,
            'document_type' => $validated['document_type'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $validated['description'] ?? null,
            'uploaded_by' => auth()->id(),
            'status' => 'pending',
            'metadata' => [
                'source' => 'medical_secretary',
                'appointment_id' => $appointment->id,
                'player_id' => $appointment->athlete?->player_id,
            ],
        ]);

        return back()->with('success', 'Document ajouté à la visite pour le médecin.');
    }

    private function appointmentTitle(string $type): string
    {
        return match ($type) {
            'consultation' => 'Consultation médicale',
            'routine_checkup' => 'Contrôle médical',
            'injury_assessment' => 'Évaluation de blessure',
            'cardiac_evaluation' => 'Évaluation cardiaque',
            'concussion_assessment' => 'Évaluation commotion',
            'follow_up' => 'Suivi médical',
            'emergency' => 'Consultation urgente',
            default => 'Rendez-vous médical',
        };
    }
}
