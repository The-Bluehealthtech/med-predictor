<?php

/**
 * Script pour ajouter le sélecteur de langue à toutes les pages
 * qui n'utilisent pas le layout principal
 */

class LanguageSwitcherAdder
{
    private $viewsPath;
    private $processedFiles = [];

    public function __construct($viewsPath = 'resources/views')
    {
        $this->viewsPath = $viewsPath;
    }

    /**
     * Ajoute le sélecteur de langue à tous les fichiers
     */
    public function addToAll()
    {
        echo "🚀 Ajout du sélecteur de langue à toutes les pages...\n";
        
        $this->scanViews($this->viewsPath);
        
        echo "✅ Terminé !\n";
        echo "📊 Fichiers traités : " . count($this->processedFiles) . "\n";
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
                $this->processFile($fullPath);
            }
        }
    }

    /**
     * Traite un fichier Blade
     */
    private function processFile($filePath)
    {
        // Ignorer les fichiers de layout et composants
        if (strpos($filePath, 'layouts/') !== false || 
            strpos($filePath, 'components/') !== false ||
            strpos($filePath, 'landing.blade.php') !== false) {
            return;
        }

        echo "📄 Traitement de : $filePath\n";
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Vérifier si le fichier utilise déjà le layout principal
        if (strpos($content, '<x-dashboard-layout>') !== false || 
            strpos($content, '@extends(') !== false ||
            strpos($content, 'x-language-switcher') !== false) {
            echo "  ⏭️  Utilise déjà le layout principal ou a déjà le sélecteur\n";
            return;
        }

        // Vérifier si c'est une page HTML complète
        if (strpos($content, '<!DOCTYPE html>') !== false || 
            strpos($content, '<html') !== false) {
            $this->addToStandalonePage($filePath, $content);
        } else {
            echo "  ⏭️  Fichier partiel, ignoré\n";
        }
    }

    /**
     * Ajoute le sélecteur à une page autonome
     */
    private function addToStandalonePage($filePath, $content)
    {
        // Chercher la balise body
        if (preg_match('/<body[^>]*>/i', $content, $matches)) {
            $bodyTag = $matches[0];
            
            // Chercher une navigation existante
            if (preg_match('/<nav[^>]*>/i', $content)) {
                // Ajouter le sélecteur dans la navigation existante
                $languageSwitcher = '
        <!-- Sélecteur de langue -->
        <div class="ml-auto">
            <x-language-switcher />
        </div>';
                
                $content = preg_replace('/<nav[^>]*>/i', '$0' . $languageSwitcher, $content);
            } else {
                // Créer une navigation simple avec le sélecteur
                $languageSwitcher = '
    <!-- Navigation avec sélecteur de langue -->
    <nav class="bg-gray-50 border-b border-gray-200 px-4 py-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset(\'images/logos/fit.png\') }}" alt="FIT Logo" style="height:80px;width:auto;margin-right:0.75rem;" class="inline-block align-middle">
            </div>
            <x-language-switcher />
        </div>
    </nav>';
                
                $content = str_replace($bodyTag, $bodyTag . $languageSwitcher, $content);
            }
            
            // Sauvegarder le fichier
            file_put_contents($filePath, $content);
            $this->processedFiles[] = $filePath;
            echo "  ✅ Sélecteur de langue ajouté\n";
        } else {
            echo "  ⏭️  Aucune balise body trouvée\n";
        }
    }
}

// Utilisation du script
if (php_sapi_name() === 'cli') {
    $adder = new LanguageSwitcherAdder();
    $adder->addToAll();
} else {
    echo "Ce script doit être exécuté en ligne de commande.\n";
    echo "Usage: php scripts/add-language-switcher.php\n";
} 