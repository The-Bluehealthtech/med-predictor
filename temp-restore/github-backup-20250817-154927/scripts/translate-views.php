<?php

/**
 * Script pour automatiser la traduction des vues Blade
 * Ce script remplace les textes en dur par des appels de traduction Laravel
 */

class ViewTranslator
{
    private $viewsPath;
    private $translations = [];
    private $processedFiles = [];

    public function __construct($viewsPath = 'resources/views')
    {
        $this->viewsPath = $viewsPath;
    }

    /**
     * Traduit tous les fichiers de vues
     */
    public function translateAll()
    {
        echo "🚀 Début de la traduction automatique des vues...\n";
        
        $this->scanViews($this->viewsPath);
        
        echo "✅ Traduction terminée !\n";
        echo "📊 Fichiers traités : " . count($this->processedFiles) . "\n";
        echo "📝 Traductions trouvées : " . count($this->translations) . "\n";
        
        // Afficher les traductions trouvées
        if (!empty($this->translations)) {
            echo "\n📋 Traductions trouvées :\n";
            foreach ($this->translations as $key => $value) {
                echo "  - $key => $value\n";
            }
        }
    }

    /**
     * Scanne récursivement les dossiers de vues
     */
    private function scanViews($path)
    {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $path . '/' . $file;
            
            if (is_dir($fullPath)) {
                $this->scanViews($fullPath);
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'blade.php') {
                $this->translateFile($fullPath);
            }
        }
    }

    /**
     * Traduit un fichier Blade
     */
    private function translateFile($filePath)
    {
        echo "📄 Traitement de : $filePath\n";
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Règles de traduction
        $translationRules = [
            // Titres et en-têtes
            '/<h1[^>]*>([^<]+)<\/h1>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'title');
            },
            '/<h2[^>]*>([^<]+)<\/h2>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'subtitle');
            },
            '/<h3[^>]*>([^<]+)<\/h3>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'heading');
            },
            
            // Boutons
            '/<button[^>]*>([^<]+)<\/button>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'button');
            },
            '/<a[^>]*>([^<]+)<\/a>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'link');
            },
            
            // Labels et textes
            '/<label[^>]*>([^<]+)<\/label>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'label');
            },
            '/<span[^>]*>([^<]+)<\/span>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'text');
            },
            '/<p[^>]*>([^<]+)<\/p>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'text');
            },
            '/<div[^>]*>([^<]+)<\/div>/i' => function($matches) {
                return $this->replaceWithTranslation($matches[1], 'text');
            },
            
            // Textes dans les attributs
            '/placeholder="([^"]+)"/i' => function($matches) {
                return 'placeholder="' . $this->getTranslationKey($matches[1], 'placeholder') . '"';
            },
            '/title="([^"]+)"/i' => function($matches) {
                return 'title="' . $this->getTranslationKey($matches[1], 'tooltip') . '"';
            },
            '/alt="([^"]+)"/i' => function($matches) {
                return 'alt="' . $this->getTranslationKey($matches[1], 'alt') . '"';
            },
        ];
        
        // Appliquer les règles de traduction
        foreach ($translationRules as $pattern => $callback) {
            $content = preg_replace_callback($pattern, $callback, $content);
        }
        
        // Sauvegarder le fichier si des changements ont été faits
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->processedFiles[] = $filePath;
            echo "  ✅ Fichier traduit\n";
        } else {
            echo "  ⏭️  Aucun changement nécessaire\n";
        }
    }

    /**
     * Remplace le texte par un appel de traduction
     */
    private function replaceWithTranslation($text, $type)
    {
        $text = trim($text);
        
        // Ignorer les textes courts ou déjà traduits
        if (strlen($text) < 3 || 
            strpos($text, '{{') !== false || 
            strpos($text, '}}') !== false ||
            strpos($text, '__(') !== false) {
            return $text;
        }
        
        $key = $this->getTranslationKey($text, $type);
        $this->translations[$key] = $text;
        
        return "{{ __('$key') }}";
    }

    /**
     * Génère une clé de traduction
     */
    private function getTranslationKey($text, $type)
    {
        // Nettoyer le texte
        $key = strtolower(trim($text));
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = preg_replace('/\s+/', '_', $key);
        $key = trim($key, '_');
        
        // Ajouter le type pour éviter les conflits
        $key = $type . '_' . $key;
        
        // Limiter la longueur
        if (strlen($key) > 50) {
            $key = substr($key, 0, 50);
        }
        
        return $key;
    }

    /**
     * Génère les fichiers de traduction
     */
    public function generateTranslationFiles()
    {
        if (empty($this->translations)) {
            echo "❌ Aucune traduction à générer\n";
            return;
        }

        // Générer le fichier anglais
        $this->generateTranslationFile('en', $this->translations);
        
        // Générer le fichier français (traduction automatique basique)
        $frenchTranslations = $this->translateToFrench($this->translations);
        $this->generateTranslationFile('fr', $frenchTranslations);
        
        echo "📝 Fichiers de traduction générés\n";
    }

    /**
     * Génère un fichier de traduction
     */
    private function generateTranslationFile($locale, $translations)
    {
        $langPath = "resources/lang/$locale";
        if (!is_dir($langPath)) {
            mkdir($langPath, 0755, true);
        }

        $content = "<?php\n\nreturn [\n";
        
        foreach ($translations as $key => $value) {
            $value = addslashes($value);
            $content .= "    '$key' => '$value',\n";
        }
        
        $content .= "];\n";
        
        $filePath = "$langPath/views.php";
        file_put_contents($filePath, $content);
        
        echo "  📄 Généré : $filePath\n";
    }

    /**
     * Traduction basique vers le français
     */
    private function translateToFrench($translations)
    {
        $frenchTranslations = [];
        
        // Dictionnaire de traduction basique
        $dictionary = [
            'title' => 'titre',
            'subtitle' => 'sous-titre',
            'heading' => 'en-tête',
            'button' => 'bouton',
            'link' => 'lien',
            'label' => 'étiquette',
            'text' => 'texte',
            'placeholder' => 'placeholder',
            'tooltip' => 'info-bulle',
            'alt' => 'alt',
            'add' => 'ajouter',
            'edit' => 'modifier',
            'delete' => 'supprimer',
            'save' => 'enregistrer',
            'cancel' => 'annuler',
            'submit' => 'soumettre',
            'search' => 'rechercher',
            'filter' => 'filtrer',
            'export' => 'exporter',
            'import' => 'importer',
            'download' => 'télécharger',
            'upload' => 'téléverser',
            'view' => 'voir',
            'create' => 'créer',
            'update' => 'mettre à jour',
            'back' => 'retour',
            'next' => 'suivant',
            'previous' => 'précédent',
            'close' => 'fermer',
            'open' => 'ouvrir',
            'active' => 'actif',
            'inactive' => 'inactif',
            'pending' => 'en attente',
            'approved' => 'approuvé',
            'rejected' => 'rejeté',
            'completed' => 'terminé',
            'cancelled' => 'annulé',
            'success' => 'succès',
            'error' => 'erreur',
            'warning' => 'avertissement',
            'info' => 'information',
            'confirm' => 'confirmer',
            'loading' => 'chargement',
            'no_data' => 'aucune donnée',
            'no_results' => 'aucun résultat',
            'dashboard' => 'tableau de bord',
            'overview' => 'aperçu',
            'statistics' => 'statistiques',
            'reports' => 'rapports',
            'analytics' => 'analyses',
            'settings' => 'paramètres',
            'profile' => 'profil',
            'notifications' => 'notifications',
            'logout' => 'déconnexion',
            'user' => 'utilisateur',
            'users' => 'utilisateurs',
            'name' => 'nom',
            'email' => 'email',
            'phone' => 'téléphone',
            'role' => 'rôle',
            'permissions' => 'permissions',
            'last_login' => 'dernière connexion',
            'created_at' => 'créé le',
            'updated_at' => 'mis à jour le',
            'player' => 'joueur',
            'players' => 'joueurs',
            'team' => 'équipe',
            'teams' => 'équipes',
            'club' => 'club',
            'clubs' => 'clubs',
            'match' => 'match',
            'matches' => 'matches',
            'competition' => 'compétition',
            'competitions' => 'compétitions',
            'season' => 'saison',
            'seasons' => 'saisons',
            'league' => 'ligue',
            'leagues' => 'ligues',
            'health' => 'santé',
            'medical' => 'médical',
            'fitness' => 'forme physique',
            'injury' => 'blessure',
            'injuries' => 'blessures',
            'recovery' => 'récupération',
            'treatment' => 'traitement',
            'assessment' => 'évaluation',
            'performance' => 'performance',
            'performances' => 'performances',
            'stats' => 'statistiques',
            'metrics' => 'métriques',
            'score' => 'score',
            'rating' => 'note',
            'rank' => 'rang',
            'ranking' => 'classement',
        ];

        foreach ($translations as $key => $value) {
            $frenchValue = $value;
            
            // Traduction basique basée sur le dictionnaire
            foreach ($dictionary as $english => $french) {
                $frenchValue = str_ireplace($english, $french, $frenchValue);
            }
            
            $frenchTranslations[$key] = $frenchValue;
        }

        return $frenchTranslations;
    }
}

// Utilisation du script
if (php_sapi_name() === 'cli') {
    $translator = new ViewTranslator();
    $translator->translateAll();
    $translator->generateTranslationFiles();
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
    echo "Usage: php scripts/translate-views.php\n";
} 