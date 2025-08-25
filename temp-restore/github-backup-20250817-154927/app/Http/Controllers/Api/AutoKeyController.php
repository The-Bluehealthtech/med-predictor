<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoKeyController extends Controller
{
    /**
     * Enregistrer une action avec une clé automatique
     */
    public function store(Request $request)
    {
        try {
            // Valider les données reçues
            $validated = $request->validate([
                'auto_key' => 'required|string',
                'action' => 'required|string',
                'module' => 'required|string|in:dtn,rpm',
                'timestamp' => 'required|date'
            ]);

            // Enregistrer l'action dans les logs
            Log::info('Action avec clé automatique', [
                'auto_key' => $validated['auto_key'],
                'action' => $validated['action'],
                'module' => $validated['module'],
                'timestamp' => $validated['timestamp'],
                'user_id' => auth()->id(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ]);

            // Ici vous pouvez ajouter la logique pour stocker en base de données
            // Par exemple, créer un modèle AutoKeyAction pour tracer les actions

            return response()->json([
                'success' => true,
                'message' => 'Action enregistrée avec succès',
                'data' => [
                    'auto_key' => $validated['auto_key'],
                    'module' => $validated['module'],
                    'timestamp' => $validated['timestamp']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement de l\'action auto-key', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de l\'action'
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques des clés automatiques
     */
    public function stats(Request $request)
    {
        try {
            $module = $request->get('module', 'all');
            
            // Ici vous pouvez ajouter la logique pour récupérer les statistiques
            // depuis la base de données
            
            $stats = [
                'total_actions' => 0,
                'auto_key1_count' => 0,
                'auto_key2_count' => 0,
                'module_breakdown' => [
                    'dtn' => 0,
                    'rpm' => 0
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques auto-key', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
} 