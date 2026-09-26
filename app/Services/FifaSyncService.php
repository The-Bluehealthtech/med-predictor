<?php

namespace App\Services;

use App\Models\Confederation;
use App\Models\Association;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FifaSyncService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.fifa.api_key', 'demo_key');
        $this->baseUrl = config('services.fifa.base_url', 'https://api.fifa.com/v1');
    }

    /**
     * Synchronise une confédération avec l'API FIFA
     */
    public function syncConfederation(Confederation $confederation)
    {
        try {
            Log::info("Début de la synchronisation FIFA pour la confédération: {$confederation->name}");

            // Appel a l'API FIFA (voir callFifaApi() : aucune API FIFA n'est configuree pour le moment)
            $response = $this->callFifaApi("/confederations/{$confederation->short_name}");

            if ($response['success']) {
                $confederation->update([
                    'fifa_ranking' => $response['data']['ranking'] ?? $confederation->fifa_ranking,
                    'fifa_version' => $response['data']['version'] ?? $confederation->fifa_version,
                    'fifa_sync_status' => 'synced',
                    'fifa_sync_date' => now(),
                    'fifa_last_error' => null,
                ]);

                Log::info("Synchronisation FIFA réussie pour: {$confederation->name}");
                return ['success' => true, 'message' => 'Synchronisation réussie'];
            } else {
                throw new \Exception($response['message'] ?? 'Erreur de synchronisation');
            }

        } catch (\Exception $e) {
            Log::error("Erreur de synchronisation FIFA pour {$confederation->name}: " . $e->getMessage());

            $confederation->update([
                'fifa_sync_status' => 'failed',
                'fifa_last_error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Synchronise une association avec l'API FIFA
     */
    public function syncAssociation(Association $association)
    {
        try {
            Log::info("Début de la synchronisation FIFA pour l'association: {$association->name}");

            // Appel a l'API FIFA (voir callFifaApi() : aucune API FIFA n'est configuree pour le moment)
            $response = $this->callFifaApi("/associations/{$association->short_name}");

            if ($response['success']) {
                $association->update([
                    'fifa_ranking' => $response['data']['ranking'] ?? $association->fifa_ranking,
                    'fifa_version' => $response['data']['version'] ?? $association->fifa_version,
                    'fifa_sync_status' => 'synced',
                    'fifa_sync_date' => now(),
                    'fifa_last_error' => null,
                ]);

                Log::info("Synchronisation FIFA réussie pour: {$association->name}");
                return ['success' => true, 'message' => 'Synchronisation réussie'];
            } else {
                throw new \Exception($response['message'] ?? 'Erreur de synchronisation');
            }

        } catch (\Exception $e) {
            Log::error("Erreur de synchronisation FIFA pour {$association->name}: " . $e->getMessage());

            $association->update([
                'fifa_sync_status' => 'failed',
                'fifa_last_error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Synchronise toutes les confédérations
     */
    public function syncAllConfederations()
    {
        $confederations = Confederation::where('status', 'active')->get();
        $results = [];

        foreach ($confederations as $confederation) {
            $results[$confederation->id] = $this->syncConfederation($confederation);
        }

        return $results;
    }

    /**
     * Synchronise toutes les associations d'une confédération
     */
    public function syncConfederationAssociations(Confederation $confederation)
    {
        $associations = $confederation->associations()->where('status', 'active')->get();
        $results = [];

        foreach ($associations as $association) {
            $results[$association->id] = $this->syncAssociation($association);
        }

        return $results;
    }

    /**
     * Appel à l'API FIFA
     *
     * NOTE (audit factice -> reel, 2026-09) : cette méthode ne faisait
     * jamais de vrai appel HTTP (malgré le "$url" calculé et la façade
     * Http importée, jamais utilisée) : elle attendait 0.5s artificiel
     * puis renvoyait un classement FIFA tiré au hasard (rand(1,6) /
     * rand(1,200) / rand(1,100)), avec une "erreur de connexion" 1 fois
     * sur 10 également tirée au hasard. Ces classements fictifs étaient
     * ensuite enregistrés comme réels dans confederations.fifa_ranking /
     * associations.fifa_ranking à chaque synchronisation. Aucune API
     * FIFA n'est configurée dans cette application
     * (services.fifa.api_key n'existe pas dans config/services.php) :
     * on renvoie donc désormais un échec honnête plutôt qu'une fausse
     * réussite avec des données inventées.
     */
    protected function callFifaApi($endpoint)
    {
        $url = $this->baseUrl . $endpoint;

        return [
            'success' => false,
            'message' => "Aucune API FIFA n'est configurée pour cette application (endpoint appelé : {$url})."
        ];
    }

    /**
     * Vérifie la connectivité avec l'API FIFA
     */
    public function checkConnectivity()
    {
        try {
            $response = $this->callFifaApi('/health');
            return [
                'connected' => $response['success'],
                'message' => $response['success'] ? 'Connecté' : 'Non connecté'
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'message' => 'Erreur de connexion: ' . $e->getMessage()
            ];
        }
    }
} 