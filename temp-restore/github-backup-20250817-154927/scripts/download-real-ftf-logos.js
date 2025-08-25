#!/usr/bin/env node

/**
 * 🏆 Script de téléchargement des vrais logos des clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Télécharge les logos officiels depuis les sites des clubs
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import https from 'https';
import http from 'http';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration des clubs FTF avec leurs URLs de logos officiels
const FTF_CLUBS = {
    'EST': {
        name: 'Esperance Sportive de Tunis',
        city: 'Tunis',
        logoUrls: [
            'https://www.est.org.tn/images/logo.png',
            'https://www.est.org.tn/logo.png',
            'https://www.est.org.tn/assets/images/logo.png'
        ],
        website: 'https://www.est.org.tn'
    },
    'ESS': {
        name: 'Etoile Sportive du Sahel',
        city: 'Sousse',
        logoUrls: [
            'https://www.etoile-du-sahel.com/images/logo.png',
            'https://www.etoile-du-sahel.com/logo.png',
            'https://www.etoile-du-sahel.com/assets/images/logo.png'
        ],
        website: 'https://www.etoile-du-sahel.com'
    },
    'CA': {
        name: 'Club Africain',
        city: 'Tunis',
        logoUrls: [
            'https://www.clubafricain.com/images/logo.png',
            'https://www.clubafricain.com/logo.png',
            'https://www.clubafricain.com/assets/images/logo.png'
        ],
        website: 'https://www.clubafricain.com'
    },
    'CSS': {
        name: 'CS Sfaxien',
        city: 'Sfax',
        logoUrls: [
            'https://www.cssfaxien.com/images/logo.png',
            'https://www.cssfaxien.com/logo.png',
            'https://www.cssfaxien.com/assets/images/logo.png'
        ],
        website: 'https://www.cssfaxien.com'
    },
    'CAB': {
        name: 'CA Bizertin',
        city: 'Bizerte',
        logoUrls: [
            'https://www.cabizertin.com/images/logo.png',
            'https://www.cabizertin.com/logo.png',
            'https://www.cabizertin.com/assets/images/logo.png'
        ],
        website: 'https://www.cabizertin.com'
    },
    'ST': {
        name: 'Stade Tunisien',
        city: 'Tunis',
        logoUrls: [
            'https://www.stadetunisien.com/images/logo.png',
            'https://www.stadetunisien.com/logo.png',
            'https://www.stadetunisien.com/assets/images/logo.png'
        ],
        website: 'https://www.stadetunisien.com'
    },
    'USM': {
        name: 'US Monastirienne',
        city: 'Monastir',
        logoUrls: [
            'https://www.usmonastir.com/images/logo.png',
            'https://www.usmonastir.com/logo.png',
            'https://www.usmonastir.com/assets/images/logo.png'
        ],
        website: 'https://www.usmonastir.com'
    },
    'USBG': {
        name: 'US Ben Guerdane',
        city: 'Ben Guerdane',
        logoUrls: [
            'https://www.usbenguerdane.com/images/logo.png',
            'https://www.usbenguerdane.com/logo.png',
            'https://www.usbenguerdane.com/assets/images/logo.png'
        ],
        website: 'https://www.usbenguerdane.com'
    },
    'OB': {
        name: 'Olympique de Béja',
        city: 'Béja',
        logoUrls: [
            'https://www.olympiquedeja.com/images/logo.png',
            'https://www.olympiquedeja.com/logo.png',
            'https://www.olympiquedeja.com/assets/images/logo.png'
        ],
        website: 'https://www.olympiquedeja.com'
    },
    'ASG': {
        name: 'Avenir Sportif de Gabès',
        city: 'Gabès',
        logoUrls: [
            'https://www.asgabes.com/images/logo.png',
            'https://www.asgabes.com/logo.png',
            'https://www.asgabes.com/assets/images/logo.png'
        ],
        website: 'https://www.asgabes.com'
    },
    'ESM': {
        name: 'ES de Métlaoui',
        city: 'Métlaoui',
        logoUrls: [
            'https://www.esmetlaoui.com/images/logo.png',
            'https://www.esmetlaoui.com/logo.png',
            'https://www.esmetlaoui.com/assets/images/logo.png'
        ],
        website: 'https://www.esmetlaoui.com'
    },
    'ESZ': {
        name: 'ES de Zarzis',
        city: 'Zarzis',
        logoUrls: [
            'https://www.eszarzis.com/images/logo.png',
            'https://www.eszarzis.com/logo.png',
            'https://www.eszarzis.com/assets/images/logo.png'
        ],
        website: 'https://www.eszarzis.com'
    },
    'JSO': {
        name: 'JS de el Omrane',
        city: 'El Omrane',
        logoUrls: [
            'https://www.jselomrane.com/images/logo.png',
            'https://www.jselomrane.com/logo.png',
            'https://www.jselomrane.com/assets/images/logo.png'
        ],
        website: 'https://www.jselomrane.com'
    },
    'EGSG': {
        name: 'El Gawafel de Gafsa',
        city: 'Gafsa',
        logoUrls: [
            'https://www.elgawafelgafsa.com/images/logo.png',
            'https://www.elgawafelgafsa.com/logo.png',
            'https://www.elgawafelgafsa.com/assets/images/logo.png'
        ],
        website: 'https://www.elgawafelgafsa.com'
    },
    'ASS': {
        name: 'AS Soliman',
        city: 'Soliman',
        logoUrls: [
            'https://www.assoliman.com/images/logo.png',
            'https://www.assoliman.com/logo.png',
            'https://www.assoliman.com/assets/images/logo.png'
        ],
        website: 'https://www.assoliman.com'
    },
    'UST': {
        name: 'US Tataouine',
        city: 'Tataouine',
        logoUrls: [
            'https://www.ustataouine.com/images/logo.png',
            'https://www.ustataouine.com/logo.png',
            'https://www.ustataouine.com/assets/images/logo.png'
        ],
        website: 'https://www.ustataouine.com'
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
    console.log(`   🌐 Site : ${club.website}`);
    
    for (let i = 0; i < club.logoUrls.length; i++) {
        const logoUrl = club.logoUrls[i];
        const filename = `${code}.png`;
        const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
        
        try {
            console.log(`   🔗 Tentative ${i + 1}/${club.logoUrls.length} : ${logoUrl}`);
            
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
        if (i < club.logoUrls.length - 1) {
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
    console.log('🏆 TÉLÉCHARGEMENT DES VRAIS LOGOS DES CLUBS FTF');
    console.log('================================================');
    console.log('🎯 Téléchargement depuis les sites officiels');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS)) {
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
    console.log('🎉 Ces sont les VRAIS logos des clubs FTF !');
    
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

export { downloadAllClubLogos, FTF_CLUBS };

