<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Athlete;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request): View
    {
        $query = Appointment::with(['athlete', 'doctor', 'createdBy']);

        // Filtres
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        $appointments = $query->orderBy('appointment_date')->get();
        $doctors = User::where('role', 'doctor')->get();
        $athletes = Athlete::all();

        // Statistiques
        $todayCount = Appointment::whereDate('appointment_date', today())->count();
        $confirmedCount = Appointment::where('status', 'Confirmé')->count();
        $pendingCount = Appointment::where('status', 'Planifié')->count();
        $cancelledCount = Appointment::where('status', 'Annulé')->count();

        return view('appointments.index', compact(
            'appointments', 
            'doctors', 
            'athletes',
            'todayCount',
            'confirmedCount',
            'pendingCount',
            'cancelledCount'
        ));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(): View
    {
        $athletes = Athlete::all();
        $doctors = User::where('role', 'doctor')->get();
        
        return view('appointments.create', compact('athletes', 'doctors'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'appointment_type' => 'required|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation,routine_checkup,injury_assessment,cardiac_evaluation,concussion_assessment',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Planifié';

        // Vérifier les conflits de planning
        $conflictingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', '<', $request->appointment_date->addMinutes($validated['duration_minutes']))
            ->where('appointment_date', '>', $request->appointment_date->subMinutes($validated['duration_minutes']))
            ->where('status', '!=', 'Annulé')
            ->first();

        if ($conflictingAppointment) {
            return back()->withErrors(['appointment_date' => 'Conflit de planning avec un autre rendez-vous.']);
        }

        Appointment::create($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous créé avec succès.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['athlete', 'doctor', 'createdBy', 'visit']);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment): View
    {
        $athletes = Athlete::all();
        $doctors = User::where('role', 'doctor')->get();
        
        return view('appointments.edit', compact('appointment', 'athletes', 'doctors'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'appointment_type' => 'required|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation,routine_checkup,injury_assessment,cardiac_evaluation,concussion_assessment',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier les conflits de planning (exclure le rendez-vous actuel)
        $conflictingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('id', '!=', $appointment->id)
            ->where('appointment_date', '<', $request->appointment_date->addMinutes($validated['duration_minutes']))
            ->where('appointment_date', '>', $request->appointment_date->subMinutes($validated['duration_minutes']))
            ->where('status', '!=', 'Annulé')
            ->first();

        if ($conflictingAppointment) {
            return back()->withErrors(['appointment_date' => 'Conflit de planning avec un autre rendez-vous.']);
        }

        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous mis à jour avec succès.');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === 'Terminé' || $appointment->visit()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un rendez-vous terminé ou avec une visite associée.']);
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous supprimé avec succès.');
    }

    /**
     * Confirm an appointment.
     */
    public function confirm(Appointment $appointment): JsonResponse
    {
        if ($appointment->confirm()) {
            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous confirmé avec succès.',
                'status' => $appointment->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible de confirmer ce rendez-vous.'
        ], 400);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment): JsonResponse
    {
        if ($appointment->cancel()) {
            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous annulé avec succès.',
                'status' => $appointment->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'annuler ce rendez-vous.'
        ], 400);
    }

    /**
     * Check-in a patient for an appointment (create visit).
     */
    public function checkIn(Appointment $appointment): JsonResponse
    {
        if (!$appointment->isCheckInReady()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce rendez-vous n\'est pas prêt pour l\'enregistrement.'
            ], 400);
        }

        // Créer la visite
        $visit = Visit::create([
            'athlete_id' => $appointment->athlete_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'visit_date' => now(),
            'visit_type' => $appointment->appointment_type,
            'status' => 'Enregistré',
        ]);

        // Mettre à jour le statut du rendez-vous
        $appointment->update(['status' => 'Enregistré']);

        return response()->json([
            'success' => true,
            'message' => 'Patient enregistré avec succès.',
            'visit_id' => $visit->id,
            'status' => $appointment->status
        ]);
    }

    /**
     * Get today's appointments for calendar view.
     */
    public function calendar(Request $request): JsonResponse
    {
        $date = $request->get('date', today());
        
        $appointments = Appointment::with(['athlete', 'doctor'])
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_date')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->athlete->name,
                    'start' => $appointment->appointment_date->format('Y-m-d H:i:s'),
                    'end' => $appointment->getEndTimeAttribute()->format('Y-m-d H:i:s'),
                    'status' => $appointment->status,
                    'type' => $appointment->appointment_type,
                    'canCheckIn' => $appointment->isCheckInReady(),
                    'doctor' => $appointment->doctor->name ?? 'Non assigné',
                    'backgroundColor' => $this->getStatusColor($appointment->status),
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Get status color for calendar events.
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'Planifié' => '#3B82F6', // Blue
            'Confirmé' => '#10B981', // Green
            'Enregistré' => '#F59E0B', // Yellow
            'En cours' => '#EF4444', // Red
            'Terminé' => '#6B7280', // Gray
            'Annulé' => '#DC2626', // Red
            'No-show' => '#7C3AED', // Purple
            default => '#6B7280',
        };
    }

    public function getAppointmentTypeLabel(string $type): string
    {
        return match ($type) {
            'consultation' => 'Consultation générale',
            'emergency' => 'Urgence',
            'follow_up' => 'Suivi',
            'pre_season' => 'Bilan pré-saison',
            'post_match' => 'Bilan post-match',
            'rehabilitation' => 'Rééducation',
            'routine_checkup' => 'Contrôle de routine',
            'injury_assessment' => 'Évaluation de blessure',
            'cardiac_evaluation' => 'Évaluation cardiaque',
            'concussion_assessment' => 'Évaluation commotion',
            default => $type,
        };
    }
}
