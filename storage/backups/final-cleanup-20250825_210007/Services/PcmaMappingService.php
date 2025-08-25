<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PcmaMappingService
{
    /**
     * Convertit les données vocales en format PCMA FIT
     */
    public function convertToPcmaFormat(array $sessionData): array
    {
        $fields = $sessionData['fields'] ?? [];
        
        return [
            'formulaire_type' => 'PCMA',
            'date_creation' => now()->format('Y-m-d H:i:s'),
            'source' => 'google_assistant',
            'donnees' => [
                'identite_joueur' => [
                    'nom' => $sessionData['player_name'] ?? 'Non spécifié',
                    'poste' => $this->mapPoste($fields['poste'] ?? ''),
                    'age' => $fields['age'] ?? null
                ],
                'antecedents_medicaux' => [
                    'a_antecedents' => $fields['antecedents'] === 'oui',
                    'derniere_blessure' => $fields['derniere_blessure'] ?? null
                ],
                'evaluation_actuelle' => [
                    'statut_medical' => $this->mapStatutMedical($fields['statut'] ?? ''),
                    'aptitude_sport' => $this->determineAptitude($fields['statut'] ?? '')
                ]
            ]
        ];
    }

    /**
     * Mappe le poste du joueur
     */
    private function mapPoste(string $poste): string
    {
        $mapping = [
            'défenseur' => 'DEF',
            'milieu' => 'MIL',
            'attaquant' => 'ATT'
        ];
        
        return $mapping[$poste] ?? 'NON_SPECIFIE';
    }

    /**
     * Mappe le statut médical
     */
    private function mapStatutMedical(string $statut): string
    {
        $mapping = [
            'apte' => 'APTE',
            'inapte temporairement' => 'INAPTE_TEMPORAIRE',
            'inapte définitivement' => 'INAPTE_DEFINITIF'
        ];

        return $mapping[$statut] ?? 'NON_EVALUE';
    }

    /**
     * Détermine l'aptitude sportive
     */
    private function determineAptitude(string $statut): string
    {
        return match($statut) {
            'apte' => 'Apte à la compétition',
            'inapte temporairement' => 'Inapte temporairement',
            'inapte définitivement' => 'Inapte définitivement',
            default => 'À évaluer'
        };
    }

    /**
     * Envoie les données PCMA à l'API FIT
     */
    public function submitToFIT(array $pcmaData): array
    {
        try {
            if (!auth()->check()) {
                return ['success' => false, 'message' => 'Utilisateur non authentifié'];
            }

            $fitData = [
                'formulaire_type' => 'PCMA',
                'donnees' => $pcmaData['donnees'],
                'utilisateur_id' => auth()->id()
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . auth()->user()->createToken('pcma-voice')->plainTextToken,
                'Content-Type' => 'application/json'
            ])->post(config('app.url') . '/api/formulaires/pcma/submit', $fitData);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PCMA soumis avec succès à FIT', ['user_id' => auth()->id()]);
                
                return [
                    'success' => true,
                    'message' => 'PCMA soumis avec succès',
                    'formulaire_id' => $result['formulaire_id'] ?? null
                ];
            } else {
                Log::error('Erreur lors de la soumission du PCMA à FIT', [
                    'status_code' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => 'Erreur lors de la soumission : ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de la soumission du PCMA à FIT', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur technique : ' . $e->getMessage()
            ];
        }
    }
}
