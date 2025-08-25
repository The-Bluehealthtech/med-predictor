<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\Appointment;
use App\Models\UploadedDocument;
use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlayerPortalController extends Controller
{
    /**
     * Obtenir le résumé du dashboard pour l'athlète connecté
     */
    public function getDashboardSummary(): JsonResponse
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        $summary = [
            'athlete' => [
                'name' => $athlete->name,
                'fifa_connect_id' => $athlete->fifa_connect_id,
                'health_score' => $athlete->health_score ?? 0,
                'last_updated' => $athlete->updated_at
            ],
            'appointments' => [
                'upcoming' => Appointment::where('fifa_connect_id', $athlete->fifa_connect_id)
                    ->where('appointment_date', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'recent' => Appointment::where('fifa_connect_id', $athlete->fifa_connect_id)
                    ->orderBy('appointment_date', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'appointment_date', 'status', 'type'])
            ],
            'documents' => [
                'total' => UploadedDocument::where('fifa_connect_id', $athlete->fifa_connect_id)->count(),
                'recent' => UploadedDocument::where('fifa_connect_id', $athlete->fifa_connect_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'document_type', 'status', 'created_at'])
            ],
            'health_records' => [
                'total' => HealthRecord::where('athlete_id', $athlete->id)->count(),
                'last_updated' => HealthRecord::where('athlete_id', $athlete->id)
                    ->orderBy('updated_at', 'desc')
                    ->value('updated_at')
            ]
        ];

        return response()->json($summary);
    }

    /**
     * Obtenir le résumé du dossier médical
     */
    public function getMedicalRecordSummary(): JsonResponse
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        $records = HealthRecord::where('athlete_id', $athlete->id)
            ->with(['visit', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $summary = [
            'athlete' => [
                'name' => $athlete->name,
                'fifa_connect_id' => $athlete->fifa_connect_id,
                'date_of_birth' => $athlete->date_of_birth,
                'blood_type' => $athlete->blood_type,
                'allergies' => $athlete->allergies
            ],
            'records' => $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'visit_date' => $record->visit->visit_date ?? null,
                    'doctor_name' => $record->doctor->name ?? 'Non spécifié',
                    'diagnosis' => $record->diagnosis,
                    'treatment' => $record->treatment,
                    'notes' => $record->notes,
                    'created_at' => $record->created_at
                ];
            }),
            'statistics' => [
                'total_records' => HealthRecord::where('athlete_id', $athlete->id)->count(),
                'this_year' => HealthRecord::where('athlete_id', $athlete->id)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'last_visit' => HealthRecord::where('athlete_id', $athlete->id)
                    ->orderBy('created_at', 'desc')
                    ->value('created_at')
            ]
        ];

        return response()->json($summary);
    }

    /**
     * Soumettre un formulaire de bien-être
     */
    public function submitWellnessForm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sleep_hours' => 'required|numeric|min:0|max:24',
            'stress_level' => 'required|integer|min:1|max:10',
            'energy_level' => 'required|integer|min:1|max:10',
            'mood' => 'required|string|in:excellent,good,neutral,bad,terrible',
            'pain_level' => 'required|integer|min:0|max:10',
            'pain_location' => 'nullable|string|max:255',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'string|max:255',
            'medication_taken' => 'nullable|array',
            'medication_taken.*' => 'string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        // Créer un enregistrement de bien-être
        $wellnessData = [
            'athlete_id' => $athlete->id,
            'fifa_connect_id' => $athlete->fifa_connect_id,
            'sleep_hours' => $request->sleep_hours,
            'stress_level' => $request->stress_level,
            'energy_level' => $request->energy_level,
            'mood' => $request->mood,
            'pain_level' => $request->pain_level,
            'pain_location' => $request->pain_location,
            'symptoms' => $request->symptoms,
            'medication_taken' => $request->medication_taken,
            'notes' => $request->notes,
            'submitted_at' => now()
        ];

        // Ici, vous pourriez sauvegarder dans une table wellness_records
        // Pour l'instant, on simule la sauvegarde
        
        // Calculer un score de bien-être
        $wellnessScore = $this->calculateWellnessScore($wellnessData);
        
        // Mettre à jour le score de santé de l'athlète
        $athlete->update([
            'health_score' => $wellnessScore,
            'last_wellness_check' => now()
        ]);

        return response()->json([
            'message' => 'Formulaire de bien-être soumis avec succès',
            'wellness_score' => $wellnessScore,
            'recommendations' => $this->getWellnessRecommendations($wellnessData)
        ], 201);
    }

    /**
     * Obtenir l'historique des formulaires de bien-être
     */
    public function getWellnessHistory(): JsonResponse
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        // Simuler l'historique des formulaires de bien-être
        $history = [
            [
                'date' => now()->subDays(1)->toDateString(),
                'sleep_hours' => 8,
                'stress_level' => 3,
                'energy_level' => 8,
                'mood' => 'good',
                'pain_level' => 1,
                'wellness_score' => 85
            ],
            [
                'date' => now()->subDays(2)->toDateString(),
                'sleep_hours' => 7,
                'stress_level' => 5,
                'energy_level' => 6,
                'mood' => 'neutral',
                'pain_level' => 2,
                'wellness_score' => 72
            ]
        ];

        return response()->json(['history' => $history]);
    }

    /**
     * Obtenir les rendez-vous de l'athlète
     */
    public function getAppointments(): JsonResponse
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        $appointments = Appointment::where('fifa_connect_id', $athlete->fifa_connect_id)
            ->with(['doctor'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        return response()->json(['appointments' => $appointments]);
    }

    /**
     * Obtenir les documents de l'athlète
     */
    public function getDocuments(): JsonResponse
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json(['error' => 'Athlète non trouvé'], 404);
        }

        $documents = UploadedDocument::where('fifa_connect_id', $athlete->fifa_connect_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['documents' => $documents]);
    }

    /**
     * Calculer le score de bien-être
     */
    private function calculateWellnessScore(array $data): int
    {
        $score = 0;
        
        // Score basé sur les heures de sommeil (0-25 points)
        if ($data['sleep_hours'] >= 7 && $data['sleep_hours'] <= 9) {
            $score += 25;
        } elseif ($data['sleep_hours'] >= 6 && $data['sleep_hours'] <= 10) {
            $score += 20;
        } else {
            $score += 10;
        }
        
        // Score basé sur le niveau de stress (0-20 points)
        $score += (11 - $data['stress_level']) * 2;
        
        // Score basé sur le niveau d'énergie (0-25 points)
        $score += $data['energy_level'] * 2.5;
        
        // Score basé sur l'humeur (0-20 points)
        $moodScores = [
            'excellent' => 20,
            'good' => 16,
            'neutral' => 12,
            'bad' => 8,
            'terrible' => 4
        ];
        $score += $moodScores[$data['mood']] ?? 10;
        
        // Score basé sur la douleur (0-10 points)
        $score += (11 - $data['pain_level']);
        
        return min(100, max(0, $score));
    }

    /**
     * Obtenir les recommandations de bien-être
     */
    private function getWellnessRecommendations(array $data): array
    {
        $recommendations = [];
        
        if ($data['sleep_hours'] < 7) {
            $recommendations[] = 'Essayez de dormir au moins 7-8 heures par nuit pour une meilleure récupération.';
        }
        
        if ($data['stress_level'] > 7) {
            $recommendations[] = 'Considérez des techniques de relaxation comme la méditation ou la respiration profonde.';
        }
        
        if ($data['energy_level'] < 6) {
            $recommendations[] = 'Assurez-vous de bien vous hydrater et de manger équilibré pour maintenir votre énergie.';
        }
        
        if ($data['pain_level'] > 5) {
            $recommendations[] = 'Consultez un professionnel de santé si la douleur persiste.';
        }
        
        if ($data['mood'] === 'bad' || $data['mood'] === 'terrible') {
            $recommendations[] = 'Parlez à un professionnel de santé mentale si vous vous sentez déprimé.';
        }
        
        return $recommendations;
    }
} 