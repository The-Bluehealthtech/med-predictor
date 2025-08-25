#!/usr/bin/env node

/**
 * ⚽ Script de téléchargement des logos officiels des fédérations
 * 📅 Créé le : 15 août 2025
 * 🎯 Objectif : Télécharger les vrais logos officiels des fédérations nationales
 */

import axios from 'axios';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Obtenir le chemin du répertoire actuel en ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/associations'),
    TIMEOUT: 30000
};

// Logos officiels des fédérations
const OFFICIAL_LOGOS = {
    'MA': {
        name: 'Fédération Royale Marocaine de Football (FRMF)',
        url: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQV-2baljpCz0zRD0FWSoAWhnxVNg0DlIsQtQ&s',
        description: 'Logo officiel de la FRMF'
    },
    'TN': {
        name: 'Fédération Tunisienne de Football (FTF)',
        url: 'https://upload.wikimedia.org/wikipedia/fr/thumb/3/33/Logo_federation_tunisienne_de_football.svg/1200px-Logo_federation_tunisienne_de_football.svg.png',
        description: 'Logo officiel de la FTF'
    },
    'DZ': {
        name: 'Fédération Algérienne de Football (FAF)',
        url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/FAF_logo.svg/1200px-FAF_logo.svg.png',
        description: 'Logo officiel de la FAF'
    },
    'FR': {
        name: 'Fédération Française de Football (FFF)',
        url: 'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8c/Logo_FFF_2019.svg/1200px-Logo_FFF_2019.svg.png',
        description: 'Logo officiel de la FFF'
    },
    'GB-ENG': {
        name: 'The Football Association (FA)',
        url: 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9d/The_Football_Association_logo.svg/1200px-The_Football_Association_logo.svg.png',
        description: 'Logo officiel de la FA'
    },
    'DE': {
        name: 'Deutscher Fußball-Bund (DFB)',
        url: 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5c/DFB_logo.svg/1200px-DFB_logo.svg.png',
        description: 'Logo officiel de la DFB'
    },
    'ES': {
        name: 'Real Federación Española de Fútbol (RFEF)',
        url: 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9d/RFEF_logo.svg/1200px-RFEF_logo.svg.png',
        description: 'Logo officiel de la RFEF'
    },
    'IT': {
        name: 'Federazione Italiana Giuoco Calcio (FIGC)',
        url: 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1b/FIGC_logo.svg/1200px-FIGC_logo.svg.png',
        description: 'Logo officiel de la FIGC'
    }
};

// Logging avec timestamp
function log(message, type = 'INFO') {
    const timestamp = new Date().toISOString();
    const prefix = type === 'ERROR' ? '❌' : type === 'SUCCESS' ? '✅' : type === 'WARNING' ? '⚠️' : 'ℹ️';
    console.log(`[${timestamp}] ${prefix} ${message}`);
}

// Créer le répertoire de sortie
function ensureOutputDirectory() {
    if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
        fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
        log(`Répertoire créé : ${CONFIG.OUTPUT_DIR}`);
    }
}

// Télécharger un logo
async function downloadLogo(countryCode, logoInfo) {
    try {
        log(`Téléchargement du logo de ${logoInfo.name} (${countryCode})`);
        log(`URL source : ${logoInfo.url}`);
        
        const response = await axios({
            method: 'GET',
            url: logoInfo.url,
            responseType: 'stream',
            timeout: CONFIG.TIMEOUT,
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept': 'image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language': 'fr-FR,fr;q=0.9,en;q=0.8',
                'Referer': 'https://www.google.com/'
            }
        });

        const outputPath = path.join(CONFIG.OUTPUT_DIR, `${countryCode}.png`);
        const writer = fs.createWriteStream(outputPath);
        response.data.pipe(writer);

        return new Promise((resolve, reject) => {
            writer.on('finish', () => {
                const stats = fs.statSync(outputPath);
                log(`✅ Logo téléchargé avec succès : ${countryCode}.png (${(stats.size / 1024).toFixed(1)} KB)`, 'SUCCESS');
                resolve(true);
            });
            writer.on('error', reject);
        });

    } catch (error) {
        log(`❌ Échec du téléchargement de ${countryCode}: ${error.message}`, 'ERROR');
        return false;
    }
}

// Fonction principale
async function main() {
    try {
        log('🚀 Démarrage du téléchargement des logos officiels des fédérations...');
        
        ensureOutputDirectory();
        
        const results = {
            success: 0,
            failed: 0,
            total: Object.keys(OFFICIAL_LOGOS).length
        };

        log(`📊 Traitement de ${results.total} fédérations...`);

        // Télécharger chaque logo
        for (const [countryCode, logoInfo] of Object.entries(OFFICIAL_LOGOS)) {
            try {
                const success = await downloadLogo(countryCode, logoInfo);
                
                if (success) {
                    results.success++;
                } else {
                    results.failed++;
                }

                // Délai entre les téléchargements
                if (results.success + results.failed < results.total) {
                    await new Promise(resolve => setTimeout(resolve, 2000));
                }

            } catch (error) {
                results.failed++;
                log(`Erreur critique pour ${countryCode}: ${error.message}`, 'ERROR');
            }
        }

        // Résumé final
        log('🏁 Téléchargement terminé !', 'SUCCESS');
        log(`📈 Résultats : ${results.success} succès, ${results.failed} échecs sur ${results.total} fédérations`);
        
        if (results.success > 0) {
            log(`✅ Logos téléchargés dans : ${CONFIG.OUTPUT_DIR}`);
            
            // Lister les fichiers téléchargés
            log('📁 Fichiers téléchargés :');
            const files = fs.readdirSync(CONFIG.OUTPUT_DIR);
            files.forEach(file => {
                if (file.endsWith('.png')) {
                    const filePath = path.join(CONFIG.OUTPUT_DIR, file);
                    const stats = fs.statSync(filePath);
                    log(`   • ${file} (${(stats.size / 1024).toFixed(1)} KB)`);
                }
            });
        }
        
        if (results.failed > 0) {
            log(`⚠️ ${results.failed} logos n'ont pas pu être téléchargés`, 'WARNING');
        }

    } catch (error) {
        log(`❌ Erreur fatale : ${error.message}`, 'ERROR');
        process.exit(1);
    }
}

// Gestion des erreurs non capturées
process.on('unhandledRejection', (reason, promise) => {
    log(`Promesse rejetée non gérée : ${reason}`, 'ERROR');
    process.exit(1);
});

process.on('uncaughtException', (error) => {
    log(`Exception non capturée : ${error.message}`, 'ERROR');
    process.exit(1);
});

// Exécuter le script
main();

