<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class VoiceProcessingService
{
    /**
     * Traite le texte vocal et extrait les informations
     */
    public function processVoiceInput(string $text, string $language = 'fr'): array
    {
        $text = trim($text);
        $textLower = strtolower($text);
        
        Log::info('Traitement vocal', [
            'text' => $text,
            'language' => $language
        ]);

        // Détection automatique de la langue si non spécifiée
        if ($language === 'auto') {
            $language = $this->detectLanguage($text);
        }

        // Traitement selon la langue
        switch ($language) {
            case 'fr':
                return $this->processFrenchText($textLower);
            case 'en':
                return $this->processEnglishText($textLower);
            case 'ar':
                return $this->processArabicText($textLower);
            default:
                return $this->processFrenchText($textLower); // Fallback français
        }
    }

    /**
     * Détecte automatiquement la langue du texte
     */
    private function detectLanguage(string $text): string
    {
        // Détection simple basée sur les caractères et mots courants
        $text = strtolower($text);
        
        // Détection arabe
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return 'ar';
        }
        
        // Détection anglaise
        $englishWords = ['the', 'and', 'is', 'are', 'player', 'position', 'age', 'medical'];
        $englishCount = 0;
        foreach ($englishWords as $word) {
            if (str_contains($text, $word)) {
                $englishCount++;
            }
        }
        
        if ($englishCount >= 2) {
            return 'en';
        }
        
        // Par défaut français
        return 'fr';
    }

    /**
     * Traite le texte français
     */
    private function processFrenchText(string $text): array
    {
        $result = [
            'language' => 'fr',
            'confidence' => 0.0,
            'extracted_data' => [],
            'suggestions' => []
        ];

        // Extraction du nom du joueur
        if (preg_match('/(?:joueur|pour|nom)\s+(?:est\s+)?([a-zA-ZÀ-ÿ\s]+?)(?:\s|$|qui|dont)/', $text, $matches)) {
            $result['extracted_data']['player_name'] = trim($matches[1]);
            $result['confidence'] += 0.3;
        }

        // Extraction du poste
        $postes = ['défenseur', 'milieu', 'attaquant', 'gardien'];
        foreach ($postes as $poste) {
            if (str_contains($text, $poste)) {
                $result['extracted_data']['position'] = $poste;
                $result['confidence'] += 0.25;
                break;
            }
        }

        // Extraction de l'âge
        if (preg_match('/(\d{1,2})\s*(?:ans?|années?)/', $text, $matches)) {
            $age = (int) $matches[1];
            if ($age >= 16 && $age <= 50) {
                $result['extracted_data']['age'] = $age;
                $result['confidence'] += 0.2;
            }
        }

        // Extraction des antécédents
        if (str_contains($text, 'oui') || str_contains($text, 'aucun') || str_contains($text, 'non')) {
            $antecedents = str_contains($text, 'non') || str_contains($text, 'aucun') ? 'non' : 'oui';
            $result['extracted_data']['antecedents'] = $antecedents;
            $result['confidence'] += 0.15;
        }

        // Extraction de la date de blessure
        $date = $this->extractDateFromFrenchText($text);
        if ($date) {
            $result['extracted_data']['last_injury'] = $date;
            $result['confidence'] += 0.1;
        }

        // Suggestions basées sur le contexte
        $result['suggestions'] = $this->generateFrenchSuggestions($result['extracted_data']);

        return $result;
    }

    /**
     * Traite le texte anglais
     */
    private function processEnglishText(string $text): array
    {
        $result = [
            'language' => 'en',
            'confidence' => 0.0,
            'extracted_data' => [],
            'suggestions' => []
        ];

        // Extraction du nom du joueur
        if (preg_match('/(?:player|for|name)\s+(?:is\s+)?([a-zA-Z\s]+?)(?:\s|$|who|whose)/', $text, $matches)) {
            $result['extracted_data']['player_name'] = trim($matches[1]);
            $result['confidence'] += 0.3;
        }

        // Extraction du poste
        $positions = ['defender', 'midfielder', 'forward', 'goalkeeper'];
        foreach ($positions as $position) {
            if (str_contains($text, $position)) {
                $result['extracted_data']['position'] = $position;
                $result['confidence'] += 0.25;
                break;
            }
        }

        // Extraction de l'âge
        if (preg_match('/(\d{1,2})\s*(?:years?|y\.?o\.?)/', $text, $matches)) {
            $age = (int) $matches[1];
            if ($age >= 16 && $age <= 50) {
                $result['extracted_data']['age'] = $age;
                $result['confidence'] += 0.2;
            }
        }

        // Extraction des antécédents
        if (str_contains($text, 'yes') || str_contains($text, 'no') || str_contains($text, 'none')) {
            $antecedents = str_contains($text, 'no') || str_contains($text, 'none') ? 'no' : 'yes';
            $result['extracted_data']['antecedents'] = $antecedents;
            $result['confidence'] += 0.15;
        }

        // Suggestions en anglais
        $result['suggestions'] = $this->generateEnglishSuggestions($result['extracted_data']);

        return $result;
    }

    /**
     * Traite le texte arabe (dialecte tunisien)
     */
    private function processArabicText(string $text): array
    {
        $result = [
            'language' => 'ar',
            'confidence' => 0.0,
            'extracted_data' => [],
            'suggestions' => []
        ];

        // Extraction du nom du joueur (patterns arabes)
        if (preg_match('/(?:لاعب|اسم|ل)/', $text)) {
            // Logique d'extraction du nom en arabe
            $result['confidence'] += 0.2;
        }

        // Extraction du poste (mots arabes courants)
        $arabicPositions = ['مدافع', 'وسط', 'مهاجم', 'حارس'];
        foreach ($arabicPositions as $position) {
            if (str_contains($text, $position)) {
                $result['extracted_data']['position'] = $position;
                $result['confidence'] += 0.25;
                break;
            }
        }

        // Extraction de l'âge (chiffres arabes)
        if (preg_match('/(\d+)\s*(?:سنة|سني)/', $text, $matches)) {
            $age = (int) $matches[1];
            if ($age >= 16 && $age <= 50) {
                $result['extracted_data']['age'] = $age;
                $result['confidence'] += 0.2;
            }
        }

        // Suggestions en arabe
        $result['suggestions'] = $this->generateArabicSuggestions($result['extracted_data']);

        return $result;
    }

    /**
     * Extrait une date du texte français
     */
    private function extractDateFromFrenchText(string $text): ?string
    {
        // Aujourd'hui
        if (str_contains($text, 'aujourd\'hui') || str_contains($text, 'aujourd hui')) {
            return now()->format('Y-m-d');
        }
        
        // Hier
        if (str_contains($text, 'hier')) {
            return now()->subDay()->format('Y-m-d');
        }
        
        // Avant-hier
        if (str_contains($text, 'avant hier') || str_contains($text, 'avant-hier')) {
            return now()->subDays(2)->format('Y-m-d');
        }
        
        // Format date française DD/MM/YYYY
        if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $text, $matches)) {
            return $matches[3] . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }
        
        // Format date française DD/MM/YY
        if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{2})/', $text, $matches)) {
            $year = '20' . $matches[3];
            return $year . '-' . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . '-' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }
        
        return null;
    }

    /**
     * Génère des suggestions en français
     */
    private function generateFrenchSuggestions(array $data): array
    {
        $suggestions = [];
        
        if (!isset($data['player_name'])) {
            $suggestions[] = 'Pouvez-vous préciser le nom du joueur ?';
        }
        
        if (!isset($data['position'])) {
            $suggestions[] = 'Quel est le poste du joueur ? (défenseur, milieu, attaquant)';
        }
        
        if (!isset($data['age'])) {
            $suggestions[] = 'Quel est l\'âge du joueur ?';
        }
        
        if (!isset($data['antecedents'])) {
            $suggestions[] = 'A-t-il des antécédents médicaux ? (oui/non)';
        }
        
        return $suggestions;
    }

    /**
     * Génère des suggestions en anglais
     */
    private function generateEnglishSuggestions(array $data): array
    {
        $suggestions = [];
        
        if (!isset($data['player_name'])) {
            $suggestions[] = 'Can you specify the player\'s name?';
        }
        
        if (!isset($data['position'])) {
            $suggestions[] = 'What is the player\'s position? (defender, midfielder, forward)';
        }
        
        if (!isset($data['age'])) {
            $suggestions[] = 'What is the player\'s age?';
        }
        
        if (!isset($data['antecedents'])) {
            $suggestions[] = 'Does he have medical history? (yes/no)';
        }
        
        return $suggestions;
    }

    /**
     * Génère des suggestions en arabe
     */
    private function generateArabicSuggestions(array $data): array
    {
        $suggestions = [];
        
        if (!isset($data['position'])) {
            $suggestions[] = 'ما هو مركز اللاعب؟ (مدافع، وسط، مهاجم)';
        }
        
        if (!isset($data['age'])) {
            $suggestions[] = 'ما هو عمر اللاعب؟';
        }
        
        return $suggestions;
    }

    /**
     * Valide la qualité de la reconnaissance vocale
     */
    public function validateVoiceQuality(array $voiceData): array
    {
        $quality = 'GOOD';
        $issues = [];
        
        if ($voiceData['confidence'] < 0.5) {
            $quality = 'POOR';
            $issues[] = 'Confiance faible dans la reconnaissance';
        }
        
        if ($voiceData['confidence'] < 0.7) {
            $quality = 'FAIR';
            $issues[] = 'Confiance modérée dans la reconnaissance';
        }
        
        if (empty($voiceData['extracted_data'])) {
            $quality = 'POOR';
            $issues[] = 'Aucune donnée extraite';
        }
        
        return [
            'quality' => $quality,
            'issues' => $issues,
            'confidence' => $voiceData['confidence']
        ];
    }

    /**
     * Nettoie et normalise le texte vocal
     */
    public function normalizeVoiceText(string $text): string
    {
        // Suppression des caractères spéciaux
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Normalisation des espaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Suppression des espaces en début et fin
        $text = trim($text);
        
        return $text;
    }
}
