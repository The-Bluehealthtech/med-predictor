<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Athlete;
use App\Models\Document;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

final class MedicalSecretaryController extends Controller
{
    public function dashboard(): View
    {
        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();

        $appointments = Appointment::with(['athlete.player', 'doctor', 'visit.documents'])
            ->where('appointment_date', '>=', $today)
            ->orderBy('appointment_date')
            ->limit(40)
            ->get();

        $recentAppointments = $appointments->take(20);
        $recentDocuments = Document::with(['visit.athlete.player', 'uploadedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'today' => $appointments->filter(fn ($a) => $a->appointment_date?->between($today, $tomorrow, false))->count(),
            'waiting' => $appointments->where('status', 'waiting')->count(),
            'in_consultation' => $appointments->where('status', 'in_progress')->count(),
            'documents_pending' => $recentDocuments->where('status', 'pending')->count(),
        ];

        $athletes = Athlete::with('player')->orderBy('name')->limit(1000)->get();
        $doctors = User::query()
            ->whereIn('role', ['club_medical', 'association_medical', 'doctor'])
            ->orderBy('name')
            ->get();

        return view('secretary.dashboard', compact(
            'stats',
            'recentAppointments',
            'recentDocuments',
            'athletes',
            'doctors'
        ));
    }

    public function storeAppointment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'appointment_type' => 'required|in:consultation,examination,follow_up,emergency',
            'doctor_id' => 'nullable|exists:users,id',
            'doctor_name' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:4000',
        ]);

        $athlete = Athlete::with('player')->findOrFail($validated['athlete_id']);
        $appointmentAt = Carbon::parse($validated['appointment_date'].' '.$validated['appointment_time']);

        Appointment::create([
            'athlete_id' => $athlete->id,
            'fifa_connect_id' => $athlete->fifa_id,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'title' => $this->appointmentTitle($validated['appointment_type']),
            'description' => $validated['reason'] ?? null,
            'appointment_date' => $appointmentAt,
            'status' => 'scheduled',
            'type' => $validated['appointment_type'],
            'location' => 'Centre médical',
            'notes' => $validated['notes'] ?? null,
            'metadata' => [
                'created_by_secretary' => auth()->id(),
                'doctor_name' => $validated['doctor_name'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'player_id' => $athlete->player_id,
            ],
        ]);

        return back()->with('success', 'Rendez-vous médical enregistré.');
    }

    public function intake(Appointment $appointment): View
    {
        $appointment->load(['athlete.player.club', 'doctor', 'visit.documents']);
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');

        $player = $appointment->athlete->player;
        $dossier = $player->baseHealthRecord()->first();

        return view('secretary.intake', compact('appointment', 'player', 'dossier'));
    }

    public function checkIn(Request $request, Appointment $appointment): RedirectResponse
    {
        $appointment->load('athlete.player');
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');

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
                'visit_type' => $appointment->type ?: 'consultation',
                'status' => 'waiting',
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

            $metadata = $appointment->metadata ?? [];
            $metadata['checked_in_at'] = now()->toIso8601String();
            $metadata['dossier_state'] = $dossier ? 'existing' : 'to_initialize';
            $metadata['visit_id'] = $visit->id;

            $appointment->update([
                'status' => 'waiting',
                'metadata' => $metadata,
            ]);
        });

        return redirect()->route('secretary.dashboard')
            ->with('success', 'Pré-accueil terminé. Le joueur est placé en salle d’attente médicale.');
    }

    public function receive(Appointment $appointment): RedirectResponse
    {
        $appointment->load(['athlete.player', 'visit']);
        abort_unless($appointment->athlete?->player, 422, 'Ce rendez-vous n’est pas relié à un joueur canonique.');

        $visit = $appointment->visit;
        abort_unless($visit, 422, 'Le pré-accueil doit être terminé avant la consultation.');

        DB::transaction(function () use ($appointment, $visit) {
            $visit->update(['status' => 'in_progress']);
            $appointment->update(['status' => 'in_progress']);
        });

        return redirect()->route('health-records.create', [
            'player_id' => $appointment->athlete->player->id,
            'appointment_id' => $appointment->id,
            'visit_id' => $visit->id,
        ]);
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
            'examination' => 'Examen spécialisé',
            'follow_up' => 'Suivi médical',
            'emergency' => 'Consultation urgente',
            default => 'Rendez-vous médical',
        };
    }
}
