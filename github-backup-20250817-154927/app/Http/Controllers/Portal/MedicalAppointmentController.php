<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MedicalAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicalAppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = MedicalAppointment::where('patient_id', auth()->id())
            ->with(['doctor'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(20);

        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'appointment_type' => 'required|in:onsite,telemedicine',
            'appointment_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:120',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $appointment = MedicalAppointment::create([
            'patient_id' => auth()->id(),
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'duration_minutes' => $request->duration_minutes,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        // Créer une notification pour les médecins
        $doctors = User::where('role', 'doctor')->get();
        foreach ($doctors as $doctor) {
            \App\Models\Notification::create([
                'user_id' => $doctor->id,
                'type' => 'appointment',
                'title' => 'Nouveau rendez-vous médical',
                'content' => 'Nouveau rendez-vous demandé par ' . auth()->user()->name,
                'action_url' => '/admin/appointments/' . $appointment->id
            ]);
        }

        return response()->json([
            'message' => 'Rendez-vous demandé avec succès',
            'data' => $appointment
        ]);
    }

    public function show(Request $request, MedicalAppointment $appointment): JsonResponse
    {
        if ($appointment->patient_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'appointment' => $appointment->load(['doctor'])
        ]);
    }

    public function update(Request $request, MedicalAppointment $appointment): JsonResponse
    {
        if ($appointment->patient_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($appointment->status !== 'pending') {
            return response()->json(['error' => 'Rendez-vous non modifiable'], 400);
        }

        $request->validate([
            'appointment_type' => 'required|in:onsite,telemedicine',
            'appointment_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:120',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000'
        ]);

        $appointment->update($request->only([
            'appointment_type', 'appointment_date', 'duration_minutes', 'reason', 'notes'
        ]));

        return response()->json([
            'message' => 'Rendez-vous modifié avec succès',
            'data' => $appointment
        ]);
    }

    public function destroy(Request $request, MedicalAppointment $appointment): JsonResponse
    {
        if ($appointment->patient_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($appointment->status !== 'pending') {
            return response()->json(['error' => 'Rendez-vous non annulable'], 400);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Rendez-vous annulé avec succès'
        ]);
    }

    public function joinVideo(Request $request, MedicalAppointment $appointment): JsonResponse
    {
        if ($appointment->patient_id !== auth()->id() && $appointment->doctor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$appointment->canStartVideo()) {
            return response()->json(['error' => 'Session vidéo non disponible'], 400);
        }

        if (!$appointment->video_meeting_id) {
            $appointment->generateVideoMeeting();
        }

        return response()->json([
            'meeting_url' => $appointment->video_meeting_url,
            'meeting_id' => $appointment->video_meeting_id,
            'meeting_password' => $appointment->video_meeting_password
        ]);
    }

    public function getAvailableDoctors(Request $request): JsonResponse
    {
        $doctors = User::where('role', 'doctor')
            ->select('id', 'name', 'email', 'specialization')
            ->get();

        return response()->json(['doctors' => $doctors]);
    }
}
