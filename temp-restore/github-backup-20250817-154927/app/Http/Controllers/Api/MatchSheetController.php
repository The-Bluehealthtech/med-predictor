<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameMatch;
use App\Models\MatchSheet;
use App\Models\MatchEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MatchSheetController extends Controller
{
    /**
     * Liste des matchs assignés à l'arbitre
     */
    public function getMatches()
    {
        $referee = auth()->user();
        
        $matches = GameMatch::where('referee_id', $referee->id)
            ->with(['homeTeam', 'awayTeam', 'competition', 'matchSheet'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('match_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $matches
        ]);
    }

    /**
     * Récupérer une feuille de match
     */
    public function getMatchSheet(GameMatch $match)
    {
        // Vérifier que l'arbitre est assigné à ce match
        if ($match->referee_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à accéder à cette feuille de match'
            ], 403);
        }

        $matchSheet = $match->matchSheet;
        
        if (!$matchSheet) {
            // Créer une nouvelle feuille de match si elle n'existe pas
            $matchSheet = MatchSheet::create([
                'match_id' => $match->id,
                'referee_id' => auth()->id(),
                'status' => 'draft',
                'home_team_score' => 0,
                'away_team_score' => 0,
            ]);
        }

        $matchSheet->load(['events.player', 'events.team']);

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match->load(['homeTeam', 'awayTeam', 'competition']),
                'match_sheet' => $matchSheet
            ]
        ]);
    }

    /**
     * Sauvegarder le brouillon de la feuille de match
     */
    public function saveDraft(Request $request, GameMatch $match)
    {
        // Vérifier que l'arbitre est assigné à ce match
        if ($match->referee_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cette feuille de match'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'home_team_score' => 'required|integer|min:0',
            'away_team_score' => 'required|integer|min:0',
            'events' => 'array',
            'events.*.type' => 'required|in:goal,yellow_card,red_card,substitution,injury',
            'events.*.minute' => 'required|integer|min:1|max:120',
            'events.*.player_id' => 'required|exists:players,id',
            'events.*.team_id' => 'required|exists:teams,id',
            'events.*.description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $matchSheet = $match->matchSheet;
            
            if (!$matchSheet) {
                $matchSheet = MatchSheet::create([
                    'match_id' => $match->id,
                    'referee_id' => auth()->id(),
                    'status' => 'draft',
                ]);
            }

            // Mettre à jour le score
            $matchSheet->update([
                'home_team_score' => $request->home_team_score,
                'away_team_score' => $request->away_team_score,
                'notes' => $request->notes,
            ]);

            // Supprimer les anciens événements
            $matchSheet->events()->delete();

            // Ajouter les nouveaux événements
            if ($request->has('events')) {
                foreach ($request->events as $eventData) {
                    MatchEvent::create([
                        'match_sheet_id' => $matchSheet->id,
                        'type' => $eventData['type'],
                        'minute' => $eventData['minute'],
                        'player_id' => $eventData['player_id'],
                        'team_id' => $eventData['team_id'],
                        'description' => $eventData['description'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brouillon sauvegardé avec succès',
                'data' => $matchSheet->load(['events.player', 'events.team'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soumettre la feuille de match pour validation
     */
    public function submit(Request $request, GameMatch $match)
    {
        // Vérifier que l'arbitre est assigné à ce match
        if ($match->referee_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à soumettre cette feuille de match'
            ], 403);
        }

        $matchSheet = $match->matchSheet;
        
        if (!$matchSheet) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune feuille de match trouvée'
            ], 404);
        }

        // Vérifier que la feuille de match n'est pas déjà soumise
        if ($matchSheet->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cette feuille de match a déjà été soumise'
            ], 400);
        }

        $matchSheet->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feuille de match soumise pour validation',
            'data' => $matchSheet
        ]);
    }

    /**
     * Valider une feuille de match (comité)
     */
    public function validate(Request $request, GameMatch $match)
    {
        $matchSheet = $match->matchSheet;
        
        if (!$matchSheet) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune feuille de match trouvée'
            ], 404);
        }

        if ($matchSheet->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Cette feuille de match n\'est pas en attente de validation'
            ], 400);
        }

        $matchSheet->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => auth()->id(),
            'validation_notes' => $request->validation_notes,
        ]);

        // Mettre à jour le statut du match
        $match->update([
            'status' => 'completed',
            'home_team_score' => $matchSheet->home_team_score,
            'away_team_score' => $matchSheet->away_team_score,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feuille de match validée',
            'data' => $matchSheet
        ]);
    }

    /**
     * Rejeter une feuille de match (comité)
     */
    public function reject(Request $request, GameMatch $match)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Raison de rejet requise',
                'errors' => $validator->errors()
            ], 422);
        }

        $matchSheet = $match->matchSheet;
        
        if (!$matchSheet) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune feuille de match trouvée'
            ], 404);
        }

        if ($matchSheet->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Cette feuille de match n\'est pas en attente de validation'
            ], 400);
        }

        $matchSheet->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feuille de match rejetée',
            'data' => $matchSheet
        ]);
    }
}
