<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Appointment;
use App\Models\Athlete;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VisitController extends Controller
{
    /**
     * Display a listing of visits.
     */
    public function index(Request $request): View
    {
        $query = Visit::with(['athlete', 'doctor', 'appointment']);

        // Filtres
        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
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

        $visits = $query->orderBy('visit_date', 'desc')->paginate(20);
        $doctors = User::where('role', 'doctor')->get();
        $athletes = Athlete::all();

        return view('visits.index', compact('visits', 'doctors', 'athletes'));
    }

    /**
     * Show the form for creating a new visit.
     */
    public function create(): View
    {
        $athletes = Athlete::all();
        $doctors = User::where('role', 'doctor')->get();
        $appointments = Appointment::where('status', 'Confirmé')
            ->whereDoesntHave('visit')
            ->with(['athlete', 'doctor'])
            ->get();
        
        return view('visits.create', compact('athletes', 'doctors', 'appointments'));
    }

    /**
     * Store a newly created visit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'visit_type' => 'required|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation,routine_checkup,injury_assessment,cardiac_evaluation,concussion_assessment',
            'notes' => 'nullable|string|max:1000',
            'administrative_data' => 'nullable|array',
        ]);

        $validated['status'] = 'Enregistré';

        // Si un rendez-vous est associé, vérifier qu'il n'a pas déjà une visite
        if (isset($validated['appointment_id']) && $validated['appointment_id']) {
            $existingVisit = Visit::where('appointment_id', $validated['appointment_id'])->first();
            if ($existingVisit) {
                return back()->withErrors(['appointment_id' => 'Ce rendez-vous a déjà une visite associée.']);
            }
        }

        $visit = Visit::create($validated);

        // Mettre à jour le statut du rendez-vous si associé
        if ($visit->appointment_id) {
            $visit->appointment->update(['status' => 'Enregistré']);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visite créée avec succès.');
    }

    /**
     * Display the specified visit.
     */
    public function show(Visit $visit): View
    {
        $visit->load([
            'athlete', 
            'doctor', 
            'appointment',
            'healthRecords',
            'pcmaRecords',
            'documents'
        ]);
        
        return view('visits.show', compact('visit'));
    }

    /**
     * Get CSS classes for visit status.
     */
    public function getStatusClasses(string $status): string
    {
        return match ($status) {
            'Enregistré' => 'bg-yellow-100 text-yellow-800',
            'En cours' => 'bg-blue-100 text-blue-800',
            'Terminé' => 'bg-green-100 text-green-800',
            'Annulé' => 'bg-red-100 text-red-800',
            'En attente' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Show the form for editing the specified visit.
     */
    public function edit(Visit $visit): View
    {
        $athletes = Athlete::all();
        $doctors = User::where('role', 'doctor')->get();
        $appointments = Appointment::where('status', 'Confirmé')
            ->where(function ($query) use ($visit) {
                $query->whereDoesntHave('visit')
                      ->orWhere('visit.id', $visit->id);
            })
            ->with(['athlete', 'doctor'])
            ->get();
        
        return view('visits.edit', compact('visit', 'athletes', 'doctors', 'appointments'));
    }

    /**
     * Update the specified visit.
     */
    public function update(Request $request, Visit $visit): RedirectResponse
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'visit_type' => 'required|in:consultation,emergency,follow_up,pre_season,post_match,rehabilitation,routine_checkup,injury_assessment,cardiac_evaluation,concussion_assessment',
            'notes' => 'nullable|string|max:1000',
            'administrative_data' => 'nullable|array',
        ]);

        // Vérifier que le nouveau rendez-vous n'a pas déjà une visite
        if (isset($validated['appointment_id']) && $validated['appointment_id'] && $validated['appointment_id'] !== $visit->appointment_id) {
            $existingVisit = Visit::where('appointment_id', $validated['appointment_id'])->first();
            if ($existingVisit) {
                return back()->withErrors(['appointment_id' => 'Ce rendez-vous a déjà une visite associée.']);
            }
        }

        $visit->update($validated);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visite mise à jour avec succès.');
    }

    /**
     * Remove the specified visit.
     */
    public function destroy(Visit $visit): RedirectResponse
    {
        if ($visit->status === 'Terminé') {
            return back()->withErrors(['error' => 'Impossible de supprimer une visite terminée.']);
        }

        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visite supprimée avec succès.');
    }

    /**
     * Start a visit (change status to 'En cours').
     */
    public function start(Visit $visit): JsonResponse
    {
        \Log::info('Starting visit', ['visit_id' => $visit->id, 'current_status' => $visit->status, 'canStart' => $visit->canStart()]);
        
        // Récupérer les données de plainte si fournies
        $complaintData = [];
        if (request()->has('complaint') && request('complaint')) {
            $complaintData['complaint'] = request('complaint');
        }
        if (request()->has('complaint_notes') && request('complaint_notes')) {
            $complaintData['complaint_notes'] = request('complaint_notes');
        }
        
        if ($visit->start()) {
            // Mettre à jour les données administratives avec les plaintes
            if (!empty($complaintData)) {
                $currentAdminData = $visit->administrative_data ?? [];
                $currentAdminData['complaint_data'] = $complaintData;
                $currentAdminData['complaint_recorded_at'] = now()->toISOString();
                
                $visit->update(['administrative_data' => $currentAdminData]);
            }
            
            \Log::info('Visit started successfully', ['visit_id' => $visit->id, 'new_status' => $visit->status, 'complaint_data' => $complaintData]);
            return response()->json([
                'success' => true,
                'message' => 'Visite démarrée avec succès.',
                'status' => $visit->status
            ]);
        }

        \Log::warning('Failed to start visit', ['visit_id' => $visit->id, 'current_status' => $visit->status]);
        return response()->json([
            'success' => false,
            'message' => 'Impossible de démarrer cette visite.'
        ], 400);
    }

    /**
     * Complete a visit (change status to 'Terminé').
     */
    public function complete(Visit $visit): JsonResponse
    {
        if ($visit->complete()) {
            return response()->json([
                'success' => true,
                'message' => 'Visite terminée avec succès.',
                'status' => $visit->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible de terminer cette visite.'
        ], 400);
    }

    /**
     * Cancel a visit.
     */
    public function cancel(Visit $visit): JsonResponse
    {
        if ($visit->cancel()) {
            return response()->json([
                'success' => true,
                'message' => 'Visite annulée avec succès.',
                'status' => $visit->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Impossible d\'annuler cette visite.'
        ], 400);
    }

    /**
     * Get today's visits for dashboard.
     */
    public function today(): JsonResponse
    {
        $visits = Visit::with(['athlete', 'doctor'])
            ->today()
            ->orderBy('visit_date')
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'athlete_name' => $visit->athlete->name,
                    'doctor_name' => $visit->doctor->name ?? 'Non assigné',
                    'visit_type' => $visit->visit_type,
                    'status' => $visit->status,
                    'visit_date' => $visit->visit_date->format('H:i'),
                    'canStart' => $visit->canStart(),
                    'canComplete' => $visit->canComplete(),
                ];
            });

        return response()->json($visits);
    }

    /**
     * Get visit statistics for dashboard.
     */
    public function statistics(): JsonResponse
    {
        $today = today();
        
        $stats = [
            'total_today' => Visit::whereDate('visit_date', $today)->count(),
            'registered_today' => Visit::whereDate('visit_date', $today)->where('status', 'Enregistré')->count(),
            'in_progress_today' => Visit::whereDate('visit_date', $today)->where('status', 'En cours')->count(),
            'completed_today' => Visit::whereDate('visit_date', $today)->where('status', 'Terminé')->count(),
            'total_this_week' => Visit::whereBetween('visit_date', [$today->startOfWeek(), $today->endOfWeek()])->count(),
            'total_this_month' => Visit::whereMonth('visit_date', $today->month)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Update administrative data for a visit.
     */
    public function updateAdministrativeData(Request $request, Visit $visit): JsonResponse
    {
        $validated = $request->validate([
            'administrative_data' => 'required|array',
            'administrative_data.billing' => 'nullable|array',
            'administrative_data.consent_forms' => 'nullable|array',
            'administrative_data.insurance' => 'nullable|array',
            'administrative_data.contact_verification' => 'nullable|array',
        ]);

        $visit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Données administratives mises à jour avec succès.',
            'data' => $visit->administrative_data
        ]);
    }
}
