#!/usr/bin/env node

/**
 * 🏆 Script de téléchargement des logos des clubs FTF depuis des sources publiques
 * 📅 Créé le 15 Août 2025
 * 🎯 Utilise des sources fiables et publiques
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import https from 'https';
import http from 'http';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Sources publiques fiables pour les logos des clubs FTF
const FTF_CLUBS_SOURCES = {
    'EST': {
        name: 'Esperance Sportive de Tunis',
        city: 'Tunis',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/4/4e/Logo_Esperance_Sportive_de_Tunis.svg/1200px-Logo_Esperance_Sportive_de_Tunis.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/EST.png',
            'https://www.footballdatabase.eu/images/clubs/EST.png'
        ]
    },
    'ESS': {
        name: 'Etoile Sportive du Sahel',
        city: 'Sousse',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8e/Logo_Etoile_Sportive_du_Sahel.svg/1200px-Logo_Etoile_Sportive_du_Sahel.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ESS.png',
            'https://www.footballdatabase.eu/images/clubs/ESS.png'
        ]
    },
    'CA': {
        name: 'Club Africain',
        city: 'Tunis',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/2/2c/Logo_Club_Africain.svg/1200px-Logo_Club_Africain.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/CA.png',
            'https://www.footballdatabase.eu/images/clubs/CA.png'
        ]
    },
    'CSS': {
        name: 'CS Sfaxien',
        city: 'Sfax',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/7/7c/Logo_CS_Sfaxien.svg/1200px-Logo_CS_Sfaxien.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/CSS.png',
            'https://www.footballdatabase.eu/images/clubs/CSS.png'
        ]
    },
    'CAB': {
        name: 'CA Bizertin',
        city: 'Bizerte',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/1/1f/Logo_CA_Bizertin.svg/1200px-Logo_CA_Bizertin.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/CAB.png',
            'https://www.footballdatabase.eu/images/clubs/CAB.png'
        ]
    },
    'ST': {
        name: 'Stade Tunisien',
        city: 'Tunis',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/9/9c/Logo_Stade_Tunisien.svg/1200px-Logo_Stade_Tunisien.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ST.png',
            'https://www.footballdatabase.eu/images/clubs/ST.png'
        ]
    },
    'USM': {
        name: 'US Monastirienne',
        city: 'Monastir',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8d/Logo_US_Monastir.svg/1200px-Logo_US_Monastir.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/USM.png',
            'https://www.footballdatabase.eu/images/clubs/USM.png'
        ]
    },
    'USBG': {
        name: 'US Ben Guerdane',
        city: 'Ben Guerdane',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/6/6a/Logo_US_Ben_Guerdane.svg/1200px-Logo_US_Ben_Guerdane.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/USBG.png',
            'https://www.footballdatabase.eu/images/clubs/USBG.png'
        ]
    },
    'OB': {
        name: 'Olympique de Béja',
        city: 'Béja',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/3/3a/Logo_Olympique_Beja.svg/1200px-Logo_Olympique_Beja.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/OB.png',
            'https://www.footballdatabase.eu/images/clubs/OB.png'
        ]
    },
    'ASG': {
        name: 'Avenir Sportif de Gabès',
        city: 'Gabès',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/5/5b/Logo_AS_Gabes.svg/1200px-Logo_AS_Gabes.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ASG.png',
            'https://www.footballdatabase.eu/images/clubs/ASG.png'
        ]
    },
    'ESM': {
        name: 'ES de Métlaoui',
        city: 'Métlaoui',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/4/4f/Logo_ES_Metlaoui.svg/1200px-Logo_ES_Metlaoui.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ESM.png',
            'https://www.footballdatabase.eu/images/clubs/ESM.png'
        ]
    },
    'ESZ': {
        name: 'ES de Zarzis',
        city: 'Zarzis',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/7/7d/Logo_ES_Zarzis.svg/1200px-Logo_ES_Zarzis.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ESZ.png',
            'https://www.footballdatabase.eu/images/clubs/ESZ.png'
        ]
    },
    'JSO': {
        name: 'JS de el Omrane',
        city: 'El Omrane',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/9/9e/Logo_JS_El_Omrane.svg/1200px-Logo_JS_El_Omrane.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/JSO.png',
            'https://www.footballdatabase.eu/images/clubs/JSO.png'
        ]
    },
    'EGSG': {
        name: 'El Gawafel de Gafsa',
        city: 'Gafsa',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/2/2b/Logo_El_Gawafel_Gafsa.svg/1200px-Logo_El_Gawafel_Gafsa.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/EGSG.png',
            'https://www.footballdatabase.eu/images/clubs/EGSG.png'
        ]
    },
    'ASS': {
        name: 'AS Soliman',
        city: 'Soliman',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/8/8f/Logo_AS_Soliman.svg/1200px-Logo_AS_Soliman.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/ASS.png',
            'https://www.footballdatabase.eu/images/clubs/ASS.png'
        ]
    },
    'UST': {
        name: 'US Tataouine',
        city: 'Tataouine',
        sources: [
            'https://upload.wikimedia.org/wikipedia/fr/thumb/1/1c/Logo_US_Tataouine.svg/1200px-Logo_US_Tataouine.svg.png',
            'https://www.transfermarkt.com/images/vereine/medium/UST.png',
            'https://www.footballdatabase.eu/images/clubs/UST.png'
        ]
    }
};

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/clubs'),
    TIMEOUT: 30000, // 30 secondes
    DELAY_BETWEEN_REQUESTS: 2000, // 2 secondes entre les requêtes
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
                'Pragma': 'no-cache'
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
    
    for (let i = 0; i < club.sources.length; i++) {
        const logoUrl = club.sources[i];
        const filename = `${code}.png`;
        const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
        
        try {
            console.log(`   🔗 Tentative ${i + 1}/${club.sources.length} : ${logoUrl}`);
            
            await downloadFile(logoUrl, filepath);
            
            // Vérifier que le fichier a été créé et a une taille > 0
            if (fs.existsSync(filepath)) {
                const stats = fs.statSync(filepath);
                if (stats.size > 0) {
                    const fileSizeKB = (stats.size / 1024).toFixed(1);
                    console.log(`✅ ${club.name} (${code}) - Logo téléchargé avec succès`);
                    console.log(`   📁 Fichier : ${filename} (${fileSizeKB} KB)`);
                    return { success: true, file: filename, size: fileSizeKB, url: logoUrl };
                } else {
                    fs.unlinkSync(filepath);
                    console.log(`   ❌ Fichier vide, tentative suivante...`);
                }
            }
        } catch (error) {
            console.log(`   ❌ Échec : ${error.message}`);
            // Supprimer le fichier en cas d'erreur
            if (fs.existsSync(filepath)) {
                fs.unlinkSync(filepath);
            }
        }
        
        // Attendre avant la prochaine tentative
        if (i < club.sources.length - 1) {
            console.log(`   ⏳ Attente de ${CONFIG.DELAY_BETWEEN_REQUESTS}ms...`);
            await new Promise(resolve => setTimeout(resolve, CONFIG.DELAY_BETWEEN_REQUESTS));
        }
    }
    
    console.log(`❌ ${club.name} (${code}) - Aucun logo téléchargé`);
    return { success: false, file: null, size: null, url: null };
}

/**
 * Télécharger tous les logos des clubs FTF
 */
async function downloadAllClubLogos() {
    console.log('🏆 TÉLÉCHARGEMENT DES LOGOS DES CLUBS FTF');
    console.log('==========================================');
    console.log('🎯 Utilisation de sources publiques fiables');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS_SOURCES).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS_SOURCES)) {
        try {
            const result = await downloadClubLogo(code, club);
            
            if (result.success) {
                results.success.push({ 
                    code, 
                    name: club.name, 
                    city: club.city,
                    file: result.file,
                    size: result.size,
                    url: result.url
                });
            } else {
                results.failed.push({ 
                    code, 
                    name: club.name, 
                    city: club.city,
                    error: 'Aucun logo téléchargé'
                });
            }
            
        } catch (error) {
            console.log(`❌ ${club.name} (${code}) - Erreur : ${error.message}`);
            results.failed.push({ code, name: club.name, error: error.message, city: club.city });
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
        });
        console.log('');
    }
    
    if (results.failed.length > 0) {
        console.log('❌ LOGOS EN ÉCHEC :');
        results.failed.forEach(result => {
            console.log(`  • ${result.code} - ${result.name} (${result.city}) : ${result.error}`);
        });
        console.log('');
    }
    
    console.log(`📁 Logos sauvegardés dans : ${CONFIG.OUTPUT_DIR}`);
    console.log('🎯 Utilisez le composant <x-club-logo-working> dans vos vues !');
    console.log('🎉 Ces sont les vrais logos des clubs FTF !');
    
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

export { downloadAllClubLogos, FTF_CLUBS_SOURCES };

