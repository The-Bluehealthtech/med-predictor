#!/usr/bin/env node

/**
 * 🏆 Script de téléchargement des vrais logos des clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Télécharge depuis worldsoccerpins.com et sources fiables
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import https from 'https';
import http from 'http';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Sources des vrais logos des clubs FTF
const FTF_CLUBS_LOGOS = {
    'EST': {
        name: 'Esperance Sportive de Tunis',
        city: 'Tunis',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39021-d6afb808/Esperance%20Sportive%20de%20Tunis.webp',
        source: 'worldsoccerpins.com'
    },
    'ESS': {
        name: 'Etoile Sportive du Sahel',
        city: 'Sousse',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39020-89ebbe76/Etoile%20Sportive%20du%20Sahel.webp',
        source: 'worldsoccerpins.com'
    },
    'CA': {
        name: 'Club Africain',
        city: 'Tunis',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39017-d93611f7/Club%20Africain.webp',
        source: 'worldsoccerpins.com'
    },
    'CSS': {
        name: 'CS Sfaxien',
        city: 'Sfax',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39016-e9b89661/CS%20Sfaxien.webp',
        source: 'worldsoccerpins.com'
    },
    'CAB': {
        name: 'CA Bizertin',
        city: 'Bizerte',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39015-947b35f7/CA%20Bizertin.webp',
        source: 'worldsoccerpins.com'
    },
    'ST': {
        name: 'Stade Tunisien',
        city: 'Tunis',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39025-667bbb12/Stade%20Tunsien.webp',
        source: 'worldsoccerpins.com'
    },
    'USM': {
        name: 'US Monastirienne',
        city: 'Monastir',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39027-007a39dd/US%20Monastirienne%20Monastir.webp',
        source: 'worldsoccerpins.com'
    },
    'USBG': {
        name: 'US Ben Guerdane',
        city: 'Ben Guerdane',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39026-03f5556a/US%20de%20Ben%20Guerdane.webp',
        source: 'worldsoccerpins.com'
    },
    'OB': {
        name: 'Olympique de Béja',
        city: 'Béja',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39024-8b1d19d6/Olympique%20de%20Be%CC%81ja.webp',
        source: 'worldsoccerpins.com'
    },
    'ASG': {
        name: 'Avenir Sportif de Gabès',
        city: 'Gabès',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39014-920af66e/Avenir%20Sportif%20de%20Gabe%CC%81s.webp',
        source: 'worldsoccerpins.com'
    },
    'ESM': {
        name: 'ES de Métlaoui',
        city: 'Métlaoui',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39019-6c404f5d/ES%20de%20Me%CC%81tlaoui.webp',
        source: 'worldsoccerpins.com'
    },
    'ESZ': {
        name: 'ES de Zarzis',
        city: 'Zarzis',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39022-768a1df9/ES%20de%20Zarzis.webp',
        source: 'worldsoccerpins.com'
    },
    'JSO': {
        name: 'JS de el Omrane',
        city: 'El Omrane',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39023-72406614/Jeunesse%20Sportive%20de%20el%20Omrane.webp',
        source: 'worldsoccerpins.com'
    },
    'EGSG': {
        name: 'El Gawafel de Gafsa',
        city: 'Gafsa',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39018-cd2d1111/El%20Gawafel%20Sportives%20de%20Gafsa.webp',
        source: 'worldsoccerpins.com'
    },
    'ASS': {
        name: 'AS Soliman',
        city: 'Soliman',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39013-69e4fa8d/AS%20Soliman.webp',
        source: 'worldsoccerpins.com'
    },
    'UST': {
        name: 'US Tataouine',
        city: 'Tataouine',
        logoUrl: 'https://www.worldsoccerpins.com/web/image/39028-26373cd9/US%20Tataouine.webp',
        source: 'worldsoccerpins.com'
    }
};

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/clubs'),
    TIMEOUT: 30000, // 30 secondes
    DELAY_BETWEEN_REQUESTS: 3000, // 3 secondes entre les requêtes pour respecter le site
    MAX_RETRIES: 3,
    USER_AGENT: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
};

// Créer le dossier de sortie s'il n'existe pas
if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
    fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
    console.log(`📁 Dossier créé : ${CONFIG.OUTPUT_DIR}`);
}

/**
 * Télécharger un fichier depuis une URL
 */
function downloadFile(url, filepath, retries = 0) {
    return new Promise((resolve, reject) => {
        const protocol = url.startsWith('https:') ? https : http;
        
        const request = protocol.get(url, {
            timeout: CONFIG.TIMEOUT,
            headers: {
                'User-Agent': CONFIG.USER_AGENT,
                'Accept': 'image/*,*/*;q=0.8',
                'Accept-Language': 'fr-FR,fr;q=0.9,en;q=0.8',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
                'Referer': 'https://www.worldsoccerpins.com/',
                'Origin': 'https://www.worldsoccerpins.com'
            }
        }, (response) => {
            if (response.statusCode === 200) {
                const fileStream = fs.createWriteStream(filepath);
                response.pipe(fileStream);
                
                fileStream.on('finish', () => {
                    fileStream.close();
                    resolve(true);
                });
                
                fileStream.on('error', (error) => {
                    fs.unlink(filepath, () => {}); // Supprimer le fichier en cas d'erreur
                    reject(error);
                });
            } else if (response.statusCode === 301 || response.statusCode === 302) {
                // Redirection
                const newUrl = response.headers.location;
                if (newUrl && retries < CONFIG.MAX_RETRIES) {
                    console.log(`   🔄 Redirection vers : ${newUrl}`);
                    downloadFile(newUrl, filepath, retries + 1)
                        .then(resolve)
                        .catch(reject);
                } else {
                    reject(new Error(`Redirection échouée après ${retries} tentatives`));
                }
            } else {
                reject(new Error(`HTTP ${response.statusCode}`));
            }
        });
        
        request.on('error', (error) => {
            reject(error);
        });
        
        request.on('timeout', () => {
            request.destroy();
            reject(new Error('Timeout'));
        });
    });
}

/**
 * Télécharger le logo d'un club
 */
async function downloadClubLogo(code, club) {
    console.log(`🏟️ Téléchargement du logo de ${club.name} (${code})...`);
    console.log(`   📍 Ville : ${club.city}`);
    console.log(`   🔗 Source : ${club.source}`);
    console.log(`   🌐 URL : ${club.logoUrl}`);
    
    const filename = `${code}.webp`; // Garder le format original
    const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
    
    try {
        await downloadFile(club.logoUrl, filepath);
        
        // Vérifier que le fichier a été créé et a une taille > 0
        if (fs.existsSync(filepath)) {
            const stats = fs.statSync(filepath);
            if (stats.size > 0) {
                const fileSizeKB = (stats.size / 1024).toFixed(1);
                console.log(`✅ ${club.name} (${code}) - Logo téléchargé avec succès`);
                console.log(`   📁 Fichier : ${filename} (${fileSizeKB} KB)`);
                return { success: true, file: filename, size: fileSizeKB, url: club.logoUrl };
            } else {
                fs.unlinkSync(filepath);
                throw new Error('Fichier vide');
            }
        } else {
            throw new Error('Fichier non créé');
        }
    } catch (error) {
        console.log(`❌ ${club.name} (${code}) - Échec : ${error.message}`);
        // Supprimer le fichier en cas d'erreur
        if (fs.existsSync(filepath)) {
            fs.unlinkSync(filepath);
        }
        return { success: false, file: null, size: null, url: club.logoUrl };
    }
}

/**
 * Télécharger tous les logos des clubs FTF
 */
async function downloadAllClubLogos() {
    console.log('🏆 TÉLÉCHARGEMENT DES VRAIS LOGOS DES CLUBS FTF');
    console.log('================================================');
    console.log('🎯 Source : worldsoccerpins.com (logos officiels)');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS_LOGOS).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS_LOGOS)) {
        try {
            const result = await downloadClubLogo(code, club);
            
            if (result.success) {
                results.success.push({ 
                    code, 
                    name: club.name, 
                    city: club.city,
                    file: result.file,
                    size: result.size,
                    url: result.url,
                    source: club.source
                });
            } else {
                results.failed.push({ 
                    code, 
                    name: club.name, 
                    city: club.city,
                    error: 'Téléchargement échoué',
                    url: result.url,
                    source: club.source
                });
            }
            
        } catch (error) {
            console.log(`❌ ${club.name} (${code}) - Erreur : ${error.message}`);
            results.failed.push({ 
                code, 
                name: club.name, 
                error: error.message, 
                city: club.city,
                url: club.logoUrl,
                source: club.source
            });
        }
        
        // Attendre entre chaque téléchargement pour respecter le site
        if (Object.keys(FTF_CLUBS_LOGOS).indexOf(code) < Object.keys(FTF_CLUBS_LOGOS).length - 1) {
            console.log(`   ⏳ Attente de ${CONFIG.DELAY_BETWEEN_REQUESTS}ms...`);
            await new Promise(resolve => setTimeout(resolve, CONFIG.DELAY_BETWEEN_REQUESTS));
        }
        
        console.log('');
    }
    
    // Résumé
    console.log('📊 RÉSUMÉ DU TÉLÉCHARGEMENT');
    console.log('==============================');
    console.log(`Total des clubs FTF : ${results.total}`);
    console.log(`✅ Succès : ${results.success.length}`);
    console.log(`❌ Échecs : ${results.failed.length}`);
    console.log('');
    
    if (results.success.length > 0) {
        console.log('🏆 LOGOS TÉLÉCHARGÉS AVEC SUCCÈS :');
        results.success.forEach(result => {
            console.log(`  • ${result.code} - ${result.name} (${result.city}) - ${result.size} KB`);
            console.log(`    📁 ${result.file} | 🌐 ${result.source}`);
        });
        console.log('');
    }
    
    if (results.failed.length > 0) {
        console.log('❌ LOGOS EN ÉCHEC :');
        results.failed.forEach(result => {
            console.log(`  • ${result.code} - ${result.name} (${result.city}) : ${result.error}`);
            console.log(`    🌐 ${result.source} | 🔗 ${result.url}`);
        });
        console.log('');
    }
    
    console.log(`📁 Logos sauvegardés dans : ${CONFIG.OUTPUT_DIR}`);
    console.log('🎯 Format : .webp (format original de worldsoccerpins.com)');
    console.log('🎯 Utilisez le composant <x-club-logo-working> dans vos vues !');
    console.log('🎉 Ces sont les VRAIS logos officiels des clubs FTF !');
    
    return results;
}

// Exécution du script
if (import.meta.url === `file://${process.argv[1]}`) {
    downloadAllClubLogos()
        .then(() => {
            console.log('🎉 Script terminé avec succès !');
            process.exit(0);
        })
        .catch((error) => {
            console.error('💥 Erreur fatale :', error);
            process.exit(1);
        });
}

export { downloadAllClubLogos, FTF_CLUBS_LOGOS };
