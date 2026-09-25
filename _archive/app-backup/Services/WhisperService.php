<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class WhisperService
{
    /**
     * Transcrit un fichier audio en texte
     */
    public function transcribeAudio($audioData): array
    {
        try {
            // Vérifier le type de données audio
            if ($audioData instanceof UploadedFile) {
                return $this->processUploadedAudio($audioData);
            } elseif (is_string($audioData) && base64_decode($audioData, true) !== false) {
                return $this->processBase64Audio($audioData);
            } else {
                return $this->processRawAudio($audioData);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la transcription Whisper', [
                'error' => $e->getMessage(),
                'audio_type' => gettype($audioData)
            ]);

            return [
                'success' => false,
                'error' => 'Erreur lors de la transcription audio',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Traite un fichier audio uploadé
     */
    private function processUploadedAudio(UploadedFile $audioFile): array
    {
        // Vérifier le type MIME
        $allowedMimes = ['audio/wav', 'audio/mp3', 'audio/m4a', 'audio/ogg'];
        if (!in_array($audioFile->getMimeType(), $allowedMimes)) {
            return [
                'success' => false,
                'error' => 'Format audio non supporté. Utilisez WAV, MP3, M4A ou OGG.'
            ];
        }

        // Sauvegarder temporairement le fichier
        $tempPath = $audioFile->store('temp/whisper', 'local');
        $fullPath = Storage::path($tempPath);

        try {
            // Traiter avec Whisper
            $result = $this->callWhisperAPI($fullPath);
            
            // Nettoyer le fichier temporaire
            Storage::delete($tempPath);
            
            return $result;
        } catch (\Exception $e) {
            // Nettoyer en cas d'erreur
            Storage::delete($tempPath);
            throw $e;
        }
    }

    /**
     * Traite un audio en base64
     */
    private function processBase64Audio(string $base64Audio): array
    {
        try {
            // Décoder le base64
            $audioData = base64_decode($base64Audio);
            if ($audioData === false) {
                return [
                    'success' => false,
                    'error' => 'Format base64 invalide'
                ];
            }

            // Sauvegarder temporairement
            $tempPath = 'temp/whisper/' . uniqid() . '.wav';
            Storage::put($tempPath, $audioData);
            $fullPath = Storage::path($tempPath);

            try {
                $result = $this->callWhisperAPI($fullPath);
                Storage::delete($tempPath);
                return $result;
            } catch (\Exception $e) {
                Storage::delete($tempPath);
                throw $e;
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors du traitement base64',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Traite des données audio brutes
     */
    private function processRawAudio($audioData): array
    {
        try {
            // Sauvegarder temporairement
            $tempPath = 'temp/whisper/' . uniqid() . '.wav';
            Storage::put($tempPath, $audioData);
            $fullPath = Storage::path($tempPath);

            try {
                $result = $this->callWhisperAPI($fullPath);
                Storage::delete($tempPath);
                return $result;
            } catch (\Exception $e) {
                Storage::delete($tempPath);
                throw $e;
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erreur lors du traitement des données audio',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Appelle l'API Whisper (OpenAI ou locale)
     */
    private function callWhisperAPI(string $audioPath): array
    {
        // Vérifier si on utilise OpenAI ou Whisper local
        if (config('whisper.use_openai', false)) {
            return $this->callOpenAIWhisper($audioPath);
        } else {
            return $this->callLocalWhisper($audioPath);
        }
    }

    /**
     * Appelle l'API OpenAI Whisper
     */
    private function callOpenAIWhisper(string $audioPath): array
    {
        try {
            $apiKey = config('whisper.openai_api_key');
            if (!$apiKey) {
                throw new \Exception('Clé API OpenAI non configurée');
            }

            // Préparer la requête
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.openai.com/v1/audio/transcriptions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: multipart/form-data'
                ],
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($audioPath),
                    'model' => 'whisper-1',
                    'language' => 'fr',
                    'response_format' => 'json'
                ]
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode !== 200) {
                throw new \Exception('Erreur API OpenAI: ' . $httpCode);
            }

            $result = json_decode($response, true);
            
            return [
                'success' => true,
                'text' => $result['text'] ?? '',
                'confidence' => 0.9, // OpenAI ne fournit pas de score de confiance
                'source' => 'openai_whisper',
                'model' => 'whisper-1'
            ];

        } catch (\Exception $e) {
            Log::error('Erreur OpenAI Whisper', [
                'error' => $e->getMessage(),
                'audio_path' => $audioPath
            ]);

            return [
                'success' => false,
                'error' => 'Erreur OpenAI Whisper: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Appelle Whisper local (si installé)
     */
    private function callLocalWhisper(string $audioPath): array
    {
        try {
            // Vérifier si Whisper est installé
            $whisperPath = config('whisper.local_path', '/usr/local/bin/whisper');
            if (!file_exists($whisperPath)) {
                throw new \Exception('Whisper local non installé. Utilisez OpenAI ou installez Whisper localement.');
            }

            // Préparer la commande
            $outputPath = dirname($audioPath) . '/output_' . uniqid() . '.txt';
            $command = sprintf(
                '%s "%s" --language French --output_dir "%s" --output_format txt',
                $whisperPath,
                $audioPath,
                dirname($audioPath)
            );

            // Exécuter la commande
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Erreur lors de l\'exécution de Whisper: ' . implode(' ', $output));
            }

            // Lire le résultat
            $transcription = '';
            if (file_exists($outputPath)) {
                $transcription = file_get_contents($outputPath);
                unlink($outputPath); // Nettoyer
            }

            return [
                'success' => true,
                'text' => trim($transcription),
                'confidence' => 0.85, // Estimation pour Whisper local
                'source' => 'local_whisper',
                'model' => 'whisper_local'
            ];

        } catch (\Exception $e) {
            Log::error('Erreur Whisper local', [
                'error' => $e->getMessage(),
                'audio_path' => $audioPath
            ]);

            return [
                'success' => false,
                'error' => 'Erreur Whisper local: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valide la qualité de l'audio
     */
    public function validateAudioQuality($audioData): array
    {
        $quality = 'GOOD';
        $issues = [];

        try {
            if ($audioData instanceof UploadedFile) {
                $size = $audioData->getSize();
                $mime = $audioData->getMimeType();

                // Vérifier la taille (max 25MB)
                if ($size > 25 * 1024 * 1024) {
                    $quality = 'POOR';
                    $issues[] = 'Fichier trop volumineux (max 25MB)';
                }

                // Vérifier le format
                $allowedMimes = ['audio/wav', 'audio/mp3', 'audio/m4a', 'audio/ogg'];
                if (!in_array($mime, $allowedMimes)) {
                    $quality = 'POOR';
                    $issues[] = 'Format audio non supporté';
                }
            }

            return [
                'quality' => $quality,
                'issues' => $issues,
                'recommendations' => $this->getQualityRecommendations($quality)
            ];

        } catch (\Exception $e) {
            return [
                'quality' => 'UNKNOWN',
                'issues' => ['Erreur lors de la validation'],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Génère des recommandations de qualité
     */
    private function getQualityRecommendations(string $quality): array
    {
        return match($quality) {
            'GOOD' => ['Audio de bonne qualité, transcription optimale attendue'],
            'FAIR' => ['Audio de qualité moyenne, transcription correcte attendue'],
            'POOR' => [
                'Audio de mauvaise qualité, transcription difficile',
                'Recommandé : enregistrer dans un environnement calme',
                'Format recommandé : WAV 16-bit, 44.1kHz'
            ],
            default => ['Qualité audio indéterminée']
        };
    }

    /**
     * Obtient les statistiques d'utilisation
     */
    public function getUsageStats(): array
    {
        return [
            'total_transcriptions' => $this->getTotalTranscriptions(),
            'success_rate' => $this->getSuccessRate(),
            'average_confidence' => $this->getAverageConfidence(),
            'fallback_usage' => $this->getFallbackUsage()
        ];
    }

    /**
     * Statistiques basiques (à implémenter avec une vraie base de données)
     */
    private function getTotalTranscriptions(): int
    {
        // TODO: Implémenter avec une table de statistiques
        return 0;
    }

    private function getSuccessRate(): float
    {
        // TODO: Implémenter avec une table de statistiques
        return 0.0;
    }

    private function getAverageConfidence(): float
    {
        // TODO: Implémenter avec une table de statistiques
        return 0.0;
    }

    private function getFallbackUsage(): int
    {
        // TODO: Implémenter avec une table de statistiques
        return 0;
    }
} 