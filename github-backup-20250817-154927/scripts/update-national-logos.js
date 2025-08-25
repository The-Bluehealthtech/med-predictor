#!/usr/bin/env node

/**
 * ⚽ Script de mise à jour des logos des fédérations nationales pour FIT
 * 📅 Créé le : 15 août 2025
 * 🎯 Objectif : Télécharger les logos officiels des fédérations nationales depuis l'API-Football
 * 
 * Ce script télécharge les logos des fédérations nationales (pas des ligues)
 * en utilisant l'endpoint /teams avec type=national
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
    API_URL: 'https://v3.football.api-sports.io/teams',
    API_KEY: 'c0c639720c2ce705387a758a71876dda',
    OUTPUT_DIR: path.join(__dirname, '../public/associations'),
    TIMEOUT: 30000, // 30 seconds
    DELAY_BETWEEN_REQUESTS: 1000, // 1 second between requests
    MAX_RETRIES: 3
};

// Mappage des codes pays vers les noms de fédérations
const FEDERATION_MAPPING = {
    'MA': 'Fédération Royale Marocaine de Football',
    'TN': 'Fédération Tunisienne de Football', 
    'DZ': 'Fédération Algérienne de Football',
    'SN': 'Fédération Sénégalaise de Football',
    'ML': 'Fédération Malienne de Football',
    'CI': 'Fédération Ivoirienne de Football',
    'NG': 'Fédération Nigériane de Football',
    'GH': 'Fédération Ghanéenne de Football',
    'CM': 'Fédération Camerounaise de Football',
    'EG': 'Fédération Égyptienne de Football',
    'FR': 'Fédération Française de Football',
    'GB-ENG': 'The Football Association',
    'DE': 'Deutscher Fußball-Bund',
    'ES': 'Real Federación Española de Fútbol',
    'IT': 'Federazione Italiana Giuoco Calcio',
    'PT': 'Federação Portuguesa de Futebol',
    'NL': 'Koninklijke Nederlandse Voetbalbond',
    'BE': 'Union Royale Belge des Sociétés de Football',
    'CH': 'Swiss Football Association',
    'AT': 'Österreichischer Fußball-Bund',
    'SE': 'Svenska Fotbollförbundet',
    'NO': 'Norges Fotballforbund',
    'DK': 'Dansk Boldspil-Union',
    'FI': 'Suomen Palloliitto',
    'PL': 'Polski Związek Piłki Nożnej',
    'CZ': 'Fotbalová asociace České republiky',
    'HU': 'Magyar Labdarúgó Szövetség',
    'RO': 'Federația Română de Fotbal',
    'BG': 'Bulgarian Football Union',
    'HR': 'Hrvatski nogometni savez',
    'RS': 'Fudbalski savez Srbije',
    'SI': 'Nogometna zveza Slovenije',
    'SK': 'Slovenský futbalový zväz',
    'LT': 'Lietuvos futbolo federacija',
    'LV': 'Latvijas Futbola federācija',
    'EE': 'Eesti Jalgpalli Liit',
    'IS': 'Knattspyrnusamband Íslands',
    'IE': 'Football Association of Ireland',
    'GB-WLS': 'Football Association of Wales',
    'GB-SCT': 'Scottish Football Association',
    'GB-NIR': 'Irish Football Association'
};

// Logging avec timestamp
function log(message, type = 'INFO') {
    const timestamp = new Date().toISOString();
    const prefix = type === 'ERROR' ? '❌' : type === 'SUCCESS' ? '✅' : type === 'WARNING' ? '⚠️' : 'ℹ️';
    console.log(`[${timestamp}] ${prefix} ${message}`);
}

// Créer le répertoire de sortie s'il n'existe pas
function ensureOutputDirectory() {
    if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
        fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
        log(`Répertoire créé : ${CONFIG.OUTPUT_DIR}`);
    }
}

// Télécharger un logo
async function downloadLogo(url, filepath, retries = 0) {
    try {
        const response = await axios({
            method: 'GET',
            url: url,
            responseType: 'stream',
            timeout: CONFIG.TIMEOUT,
            headers: {
                'x-apisports-key': CONFIG.API_KEY
            }
        });

        const writer = fs.createWriteStream(filepath);
        response.data.pipe(writer);

        return new Promise((resolve, reject) => {
            writer.on('finish', () => {
                const stats = fs.statSync(filepath);
                log(`Logo téléchargé : ${path.basename(filepath)} (${(stats.size / 1024).toFixed(1)} KB)`);
                resolve();
            });
            writer.on('error', reject);
        });

    } catch (error) {
        if (retries < CONFIG.MAX_RETRIES) {
            log(`Tentative ${retries + 1} échouée pour ${path.basename(filepath)}, nouvelle tentative...`, 'WARNING');
            await new Promise(resolve => setTimeout(resolve, CONFIG.DELAY_BETWEEN_REQUESTS));
            return downloadLogo(url, filepath, retries + 1);
        }
        
        log(`Échec du téléchargement de ${path.basename(filepath)} après ${CONFIG.MAX_RETRIES} tentatives: ${error.message}`, 'ERROR');
        throw error;
    }
}

// Traiter une fédération nationale
async function processNationalFederation(countryCode, federationName) {
    try {
        log(`Recherche de la fédération : ${federationName} (${countryCode})`);
        
        // Rechercher l'équipe nationale
        const response = await axios({
            method: 'GET',
            url: CONFIG.API_URL,
            params: {
                country: countryCode,
                type: 'national'
            },
            headers: {
                'x-apisports-key': CONFIG.API_KEY
            },
            timeout: CONFIG.TIMEOUT
        });

        if (response.data && response.data.response && response.data.response.length > 0) {
            const team = response.data.response[0];
            
            if (team.team && team.team.logo) {
                const logoUrl = team.team.logo;
                const outputPath = path.join(CONFIG.OUTPUT_DIR, `${countryCode}.png`);
                
                log(`Logo trouvé pour ${federationName}: ${logoUrl}`);
                await downloadLogo(logoUrl, outputPath);
                return true;
            } else {
                log(`Aucun logo trouvé pour ${federationName}`, 'WARNING');
                return false;
            }
        } else {
            log(`Aucune équipe nationale trouvée pour ${countryCode}`, 'WARNING');
            return false;
        }

    } catch (error) {
        log(`Erreur lors du traitement de ${federationName} (${countryCode}): ${error.message}`, 'ERROR');
        return false;
    }
}

// Fonction principale
async function main() {
    try {
        log('🚀 Démarrage de la mise à jour des logos des fédérations nationales...');
        
        ensureOutputDirectory();
        
        const results = {
            success: 0,
            failed: 0,
            total: Object.keys(FEDERATION_MAPPING).length
        };

        log(`📊 Traitement de ${results.total} fédérations nationales...`);

        // Traiter chaque fédération
        for (const [countryCode, federationName] of Object.entries(FEDERATION_MAPPING)) {
            try {
                const success = await processNationalFederation(countryCode, federationName);
                
                if (success) {
                    results.success++;
                } else {
                    results.failed++;
                }

                // Délai entre les requêtes pour éviter la limitation de l'API
                if (results.success + results.failed < results.total) {
                    await new Promise(resolve => setTimeout(resolve, CONFIG.DELAY_BETWEEN_REQUESTS));
                }

            } catch (error) {
                results.failed++;
                log(`Erreur critique pour ${federationName}: ${error.message}`, 'ERROR');
            }
        }

        // Résumé final
        log('🏁 Mise à jour terminée !', 'SUCCESS');
        log(`📈 Résultats : ${results.success} succès, ${results.failed} échecs sur ${results.total} fédérations`);
        
        if (results.success > 0) {
            log(`✅ Logos téléchargés dans : ${CONFIG.OUTPUT_DIR}`);
        }
        
        if (results.failed > 0) {
            log(`⚠️ ${results.failed} fédérations n'ont pas pu être traitées`, 'WARNING');
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
