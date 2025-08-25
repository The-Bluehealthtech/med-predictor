#!/usr/bin/env node

/**
 * 🧪 Script de test des vrais logos des clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Vérifie que tous les logos téléchargés sont accessibles
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import https from 'https';
import http from 'http';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const CONFIG = {
    CLUBS_DIR: path.join(__dirname, '../public/clubs'),
    BASE_URL: 'http://localhost:8001',
    TIMEOUT: 10000
};

// Liste des clubs FTF
const FTF_CLUBS = {
    'EST': 'Esperance Sportive de Tunis',
    'ESS': 'Etoile Sportive du Sahel',
    'CA': 'Club Africain',
    'CSS': 'CS Sfaxien',
    'CAB': 'CA Bizertin',
    'ST': 'Stade Tunisien',
    'USM': 'US Monastirienne',
    'USBG': 'US Ben Guerdane',
    'OB': 'Olympique de Béja',
    'ASG': 'Avenir Sportif de Gabès',
    'ESM': 'ES de Métlaoui',
    'ESZ': 'ES de Zarzis',
    'JSO': 'JS de el Omrane',
    'EGSG': 'El Gawafel de Gafsa',
    'ASS': 'AS Soliman',
    'UST': 'US Tataouine'
};

/**
 * Vérifier l'existence d'un fichier
 */
function checkFileExists(filepath) {
    try {
        if (fs.existsSync(filepath)) {
            const stats = fs.statSync(filepath);
            return {
                exists: true,
                size: stats.size,
                sizeKB: (stats.size / 1024).toFixed(1),
                modified: stats.mtime
            };
        }
        return { exists: false };
    } catch (error) {
        return { exists: false, error: error.message };
    }
}

/**
 * Tester l'accessibilité d'une URL
 */
function testUrl(url) {
    return new Promise((resolve) => {
        const protocol = url.startsWith('https:') ? https : http;
        
        const request = protocol.get(url, { timeout: CONFIG.TIMEOUT }, (response) => {
            resolve({
                status: response.statusCode,
                accessible: response.statusCode === 200,
                contentType: response.headers['content-type'] || 'unknown',
                contentLength: response.headers['content-length'] || 'unknown'
            });
        });
        
        request.on('error', (error) => {
            resolve({
                status: 'ERROR',
                accessible: false,
                error: error.message
            });
        });
        
        request.on('timeout', () => {
            request.destroy();
            resolve({
                status: 'TIMEOUT',
                accessible: false,
                error: 'Timeout'
            });
        });
    });
}

/**
 * Tester tous les logos des clubs FTF
 */
async function testAllClubLogos() {
    console.log('🧪 TEST DES VRAIS LOGOS DES CLUBS FTF');
    console.log('=====================================');
    console.log('');
    
    const results = {
        total: Object.keys(FTF_CLUBS).length,
        webp: 0,
        png: 0,
        missing: 0,
        accessible: 0,
        inaccessible: 0,
        details: []
    };
    
    for (const [code, name] of Object.entries(FTF_CLUBS)) {
        console.log(`🏟️ Test du logo ${code} - ${name}...`);
        
        const webpPath = path.join(CONFIG.CLUBS_DIR, `${code}.webp`);
        const pngPath = path.join(CONFIG.CLUBS_DIR, `${code}.png`);
        
        // Vérifier les fichiers
        const webpCheck = checkFileExists(webpPath);
        const pngCheck = checkFileExists(pngPath);
        
        let logoType = 'none';
        let fileInfo = null;
        
        if (webpCheck.exists) {
            logoType = 'webp';
            fileInfo = webpCheck;
            results.webp++;
        } else if (pngCheck.exists) {
            logoType = 'png';
            fileInfo = pngCheck;
            results.png++;
        } else {
            results.missing++;
        }
        
        // Tester l'accessibilité web
        let webAccess = null;
        if (fileInfo && fileInfo.exists) {
            const webUrl = `${CONFIG.BASE_URL}/clubs/${code}.${logoType}`;
            console.log(`   🌐 Test d'accessibilité : ${webUrl}`);
            webAccess = await testUrl(webUrl);
            
            if (webAccess.accessible) {
                results.accessible++;
            } else {
                results.inaccessible++;
            }
        }
        
        // Résumé pour ce club
        const clubResult = {
            code,
            name,
            logoType,
            fileInfo,
            webAccess,
            status: logoType !== 'none' ? '✅' : '❌'
        };
        
        results.details.push(clubResult);
        
        // Affichage du résultat
        if (logoType !== 'none') {
            console.log(`   ${clubResult.status} Logo ${logoType.toUpperCase()} trouvé (${fileInfo.sizeKB} KB)`);
            if (webAccess) {
                if (webAccess.accessible) {
                    console.log(`   ✅ Accessible via le web (${webAccess.status})`);
                } else {
                    console.log(`   ❌ Non accessible : ${webAccess.error || webAccess.status}`);
                }
            }
        } else {
            console.log(`   ❌ Aucun logo trouvé`);
        }
        
        console.log('');
    }
    
    // Résumé global
    console.log('📊 RÉSUMÉ DES TESTS');
    console.log('====================');
    console.log(`Total des clubs FTF : ${results.total}`);
    console.log(`✅ Logos WebP : ${results.webp}`);
    console.log(`📁 Logos PNG : ${results.png}`);
    console.log(`❌ Logos manquants : ${results.missing}`);
    console.log(`🌐 Accessibles via le web : ${results.accessible}`);
    console.log(`🚫 Non accessibles via le web : ${results.inaccessible}`);
    console.log('');
    
    // Détails par club
    console.log('🏆 DÉTAILS PAR CLUB :');
    results.details.forEach(club => {
        const status = club.status;
        const logoInfo = club.logoType !== 'none' ? `${club.logoType.toUpperCase()} (${club.fileInfo.sizeKB} KB)` : 'Manquant';
        const webStatus = club.webAccess ? (club.webAccess.accessible ? '✅ Web OK' : '❌ Web KO') : 'N/A';
        
        console.log(`  ${status} ${club.code} - ${club.name}`);
        console.log(`     Logo : ${logoInfo}`);
        console.log(`     Web : ${webStatus}`);
    });
    
    console.log('');
    console.log(`📁 Dossier des logos : ${CONFIG.CLUBS_DIR}`);
    console.log(`🌐 URL de test : ${CONFIG.BASE_URL}/demo-vrais-logos-ftf`);
    
    return results;
}

// Exécution du script
if (import.meta.url === `file://${process.argv[1]}`) {
    testAllClubLogos()
        .then(() => {
            console.log('🎉 Test terminé !');
            process.exit(0);
        })
        .catch((error) => {
            console.error('💥 Erreur fatale :', error);
            process.exit(1);
        });
}

export { testAllClubLogos, FTF_CLUBS };
