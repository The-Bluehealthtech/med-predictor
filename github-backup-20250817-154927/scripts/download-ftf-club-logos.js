#!/usr/bin/env node

/**
 * 🏆 Script de téléchargement des logos des clubs FTF (Fédération Tunisienne de Football) - Ligue 1
 * 📅 Créé le 15 Août 2025
 * 🎯 Basé sur l'expérience réussie des logos des fédérations
 * 🌐 Source : https://www.worldsoccerpins.com/football-logos-tunisia
 */

import fs from 'fs';
import path from 'path';
import https from 'https';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration des clubs FTF en Ligue 1 - Basée sur worldsoccerpins.com
const FTF_CLUBS = {
    'EST': {
        name: 'Esperance Sportive de Tunis',
        fullName: 'Esperance Sportive de Tunis',
        city: 'Tunis',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/esperance-sportive-de-tunis',
        description: 'Logo officiel de l\'Esperance Sportive de Tunis'
    },
    'ESS': {
        name: 'Etoile Sportive du Sahel',
        fullName: 'Etoile Sportive du Sahel',
        city: 'Sousse',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/etoile-sportive-du-sahel',
        description: 'Logo officiel de l\'Etoile Sportive du Sahel'
    },
    'CA': {
        name: 'Club Africain',
        fullName: 'Club Africain de Tunis',
        city: 'Tunis',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/club-africain',
        description: 'Logo officiel du Club Africain'
    },
    'CSS': {
        name: 'CS Sfaxien',
        fullName: 'Club Sportif Sfaxien',
        city: 'Sfax',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/cs-sfaxien',
        description: 'Logo officiel du Club Sportif Sfaxien'
    },
    'CAB': {
        name: 'CA Bizertin',
        fullName: 'Club Athlétique Bizertin',
        city: 'Bizerte',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/ca-bizertin',
        description: 'Logo officiel du Club Athlétique Bizertin'
    },
    'ST': {
        name: 'Stade Tunisien',
        fullName: 'Stade Tunisien',
        city: 'Tunis',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/stade-tunisien',
        description: 'Logo officiel du Stade Tunisien'
    },
    'USM': {
        name: 'US Monastirienne',
        fullName: 'Union Sportive Monastirienne',
        city: 'Monastir',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/us-monastirienne',
        description: 'Logo officiel de l\'Union Sportive Monastirienne'
    },
    'USBG': {
        name: 'US Ben Guerdane',
        fullName: 'Union Sportive de Ben Guerdane',
        city: 'Ben Guerdane',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/us-ben-guerdane',
        description: 'Logo officiel de l\'Union Sportive de Ben Guerdane'
    },
    'OB': {
        name: 'Olympique de Béja',
        fullName: 'Olympique de Béja',
        city: 'Béja',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/olympique-de-beja',
        description: 'Logo officiel de l\'Olympique de Béja'
    },
    'ASG': {
        name: 'Avenir Sportif de Gabès',
        fullName: 'Avenir Sportif de Gabès',
        city: 'Gabès',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/avenir-sportif-de-gabes',
        description: 'Logo officiel de l\'Avenir Sportif de Gabès'
    },
    'ESM': {
        name: 'ES de Métlaoui',
        fullName: 'Étoile Sportive de Métlaoui',
        city: 'Métlaoui',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/es-de-metlaoui',
        description: 'Logo officiel de l\'Étoile Sportive de Métlaoui'
    },
    'ESZ': {
        name: 'ES de Zarzis',
        fullName: 'Étoile Sportive de Zarzis',
        city: 'Zarzis',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/es-de-zarzis',
        description: 'Logo officiel de l\'Étoile Sportive de Zarzis'
    },
    'JSO': {
        name: 'Jeunesse Sportive de el Omrane',
        fullName: 'Jeunesse Sportive de el Omrane',
        city: 'El Omrane',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/jeunesse-sportive-de-el-omrane',
        description: 'Logo officiel de la Jeunesse Sportive de el Omrane'
    },
    'EGSG': {
        name: 'El Gawafel Sportives de Gafsa',
        fullName: 'El Gawafel Sportives de Gafsa',
        city: 'Gafsa',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/el-gawafel-sportives-de-gafsa',
        description: 'Logo officiel d\'El Gawafel Sportives de Gafsa'
    },
    'ASS': {
        name: 'AS Soliman',
        fullName: 'Association Sportive de Soliman',
        city: 'Soliman',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/as-soliman',
        description: 'Logo officiel de l\'Association Sportive de Soliman'
    },
    'UST': {
        name: 'US Tataouine',
        fullName: 'Union Sportive de Tataouine',
        city: 'Tataouine',
        url: 'https://www.worldsoccerpins.com/football-logos-tunisia/us-tataouine',
        description: 'Logo officiel de l\'Union Sportive de Tataouine'
    }
};

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/clubs'),
    TIMEOUT: 30000, // 30 secondes
    DELAY_BETWEEN_REQUESTS: 2000, // 2 secondes entre les requêtes (respecter le site)
    MAX_RETRIES: 3
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
        const file = fs.createWriteStream(filepath);
        
        const request = https.get(url, (response) => {
            if (response.statusCode === 200) {
                response.pipe(file);
                file.on('finish', () => {
                    file.close();
                    resolve(true);
                });
            } else if (response.statusCode === 301 || response.statusCode === 302) {
                // Redirection
                const newUrl = response.headers.location;
                console.log(`🔄 Redirection vers : ${newUrl}`);
                file.close();
                fs.unlinkSync(filepath);
                if (retries < CONFIG.MAX_RETRIES) {
                    setTimeout(() => {
                        downloadFile(newUrl, filepath, retries + 1)
                            .then(resolve)
                            .catch(reject);
                    }, CONFIG.DELAY_BETWEEN_REQUESTS);
                } else {
                    reject(new Error(`Trop de redirections pour ${url}`));
                }
            } else {
                file.close();
                fs.unlinkSync(filepath);
                reject(new Error(`HTTP ${response.statusCode} pour ${url}`));
            }
        });
        
        request.setTimeout(CONFIG.TIMEOUT, () => {
            request.destroy();
            file.close();
            fs.unlinkSync(filepath);
            reject(new Error(`Timeout pour ${url}`));
        });
        
        request.on('error', (err) => {
            file.close();
            if (fs.existsSync(filepath)) {
                fs.unlinkSync(filepath);
            }
            reject(err);
        });
        
        file.on('error', (err) => {
            file.close();
            if (fs.existsSync(filepath)) {
                fs.unlinkSync(filepath);
            }
            reject(err);
        });
    });
}

/**
 * Télécharger tous les logos des clubs FTF
 */
async function downloadAllClubLogos() {
    console.log('🏆 TÉLÉCHARGEMENT DES LOGOS DES CLUBS FTF - LIGUE 1');
    console.log('=====================================================');
    console.log('🌐 Source : https://www.worldsoccerpins.com/football-logos-tunisia');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS)) {
        console.log(`🏟️ Téléchargement du logo de ${club.fullName} (${code})...`);
        console.log(`   📍 Ville : ${club.city}`);
        console.log(`   🔗 URL : ${club.url}`);
        
        try {
            const filename = `${code}.png`;
            const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
            
            await downloadFile(club.url, filepath);
            
            // Vérifier que le fichier a été créé et a une taille > 0
            if (fs.existsSync(filepath) && fs.statSync(filepath).size > 0) {
                const stats = fs.statSync(filepath);
                const sizeKB = (stats.size / 1024).toFixed(1);
                
                console.log(`✅ ${club.fullName} (${code}) - ${sizeKB} KB`);
                results.success.push({ code, name: club.fullName, size: sizeKB, city: club.city });
            } else {
                throw new Error('Fichier vide ou non créé');
            }
            
        } catch (error) {
            console.log(`❌ ${club.fullName} (${code}) - Erreur : ${error.message}`);
            results.failed.push({ code, name: club.fullName, error: error.message, city: club.city });
        }
        
        // Délai entre les requêtes pour respecter le site
        if (Object.keys(FTF_CLUBS).indexOf(code) < Object.keys(FTF_CLUBS).length - 1) {
            console.log(`⏳ Attente de ${CONFIG.DELAY_BETWEEN_REQUESTS}ms...`);
            await new Promise(resolve => setTimeout(resolve, CONFIG.DELAY_BETWEEN_REQUESTS));
        }
        
        console.log('');
    }
    
    // Résumé
    console.log('📊 RÉSUMÉ DU TÉLÉCHARGEMENT');
    console.log('=============================');
    console.log(`Total des clubs FTF : ${results.total}`);
    console.log(`✅ Succès : ${results.success.length}`);
    console.log(`❌ Échecs : ${results.failed.length}`);
    console.log('');
    
    if (results.success.length > 0) {
        console.log('🏆 LOGOS TÉLÉCHARGÉS AVEC SUCCÈS :');
        results.success.forEach(result => {
            console.log(`  • ${result.code}.png - ${result.name} (${result.city}) - ${result.size} KB`);
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
    console.log('🌐 Source officielle : https://www.worldsoccerpins.com/football-logos-tunisia');
    
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
