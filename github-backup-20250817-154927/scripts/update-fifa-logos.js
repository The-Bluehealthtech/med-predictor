#!/usr/bin/env node

/**
 * 🏆 Script de mise à jour des logos FIFA pour FIT
 * 📅 Créé le : $(date)
 * 🎯 Objectif : Récupérer et synchroniser les logos des associations FIFA
 */

import https from 'https';
import http from 'http';
import fs from 'fs';
import path from 'path';
import { URL } from 'url';
import { fileURLToPath } from 'url';

// Obtenir le chemin du répertoire actuel en ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const CONFIG = {
    FIFA_API_URL: 'https://api.fifa.com/api/v3/association',
    OUTPUT_DIR: path.join(__dirname, '../public/associations'),
    LOGO_EXTENSIONS: ['.png', '.svg', '.jpg', '.jpeg'],
    TIMEOUT: 30000, // 30 secondes
    RETRY_ATTEMPTS: 3,
    CONCURRENT_DOWNLOADS: 5, // Limiter les téléchargements simultanés
    USER_AGENT: 'FIT-Application/1.0 (FIFA-Logo-Sync)'
};

// Variables globales
let totalAssociations = 0;
let downloadedLogos = 0;
let failedLogos = 0;
let skippedLogos = 0;

/**
 * Créer le répertoire de sortie s'il n'existe pas
 */
function ensureOutputDirectory() {
    if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
        fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
        console.log(`📁 Répertoire créé : ${CONFIG.OUTPUT_DIR}`);
    }
}

/**
 * Effectuer une requête HTTP/HTTPS
 */
function makeRequest(url, options = {}) {
    return new Promise((resolve, reject) => {
        const urlObj = new URL(url);
        const isHttps = urlObj.protocol === 'https:';
        const client = isHttps ? https : http;
        
        const requestOptions = {
            hostname: urlObj.hostname,
            port: urlObj.port || (isHttps ? 443 : 80),
            path: urlObj.pathname + urlObj.search,
            method: 'GET',
            headers: {
                'User-Agent': CONFIG.USER_AGENT,
                'Accept': 'image/*,application/json,*/*',
                'Accept-Language': 'en-US,en;q=0.9',
                ...options.headers
            },
            timeout: CONFIG.TIMEOUT
        };

        const req = client.request(requestOptions, (res) => {
            let data = '';
            
            res.on('data', (chunk) => {
                data += chunk;
            });
            
            res.on('end', () => {
                if (res.statusCode >= 200 && res.statusCode < 300) {
                    resolve({
                        statusCode: res.statusCode,
                        headers: res.headers,
                        data: data
                    });
                } else {
                    reject(new Error(`HTTP ${res.statusCode}: ${res.statusMessage}`));
                }
            });
        });

        req.on('error', (error) => {
            reject(error);
        });

        req.on('timeout', () => {
            req.destroy();
            reject(new Error('Request timeout'));
        });

        req.end();
    });
}

/**
 * Télécharger un logo avec gestion des erreurs et retry
 */
async function downloadLogo(logoUrl, outputPath, associationCode, retryCount = 0) {
    try {
        console.log(`⬇️  Téléchargement de ${associationCode} depuis ${logoUrl}`);
        
        const response = await makeRequest(logoUrl);
        
        // Vérifier le type de contenu
        const contentType = response.headers['content-type'] || '';
        if (!contentType.startsWith('image/')) {
            throw new Error(`Contenu non-image reçu: ${contentType}`);
        }
        
        // Déterminer l'extension basée sur le type de contenu
        let extension = '.png'; // Par défaut
        if (contentType.includes('svg')) extension = '.svg';
        else if (contentType.includes('jpeg') || contentType.includes('jpg')) extension = '.jpg';
        
        const finalOutputPath = outputPath.replace(/\.[^/.]+$/, '') + extension;
        
        // Écrire le fichier
        fs.writeFileSync(finalOutputPath, response.data);
        
        console.log(`✅ Logo téléchargé : ${associationCode} → ${finalOutputPath}`);
        downloadedLogos++;
        
        return finalOutputPath;
        
    } catch (error) {
        if (retryCount < CONFIG.RETRY_ATTEMPTS) {
            console.log(`⚠️  Tentative ${retryCount + 1} échouée pour ${associationCode}, nouvelle tentative...`);
            await new Promise(resolve => setTimeout(resolve, 1000 * (retryCount + 1))); // Délai progressif
            return downloadLogo(logoUrl, outputPath, associationCode, retryCount + 1);
        } else {
            console.error(`❌ Échec du téléchargement de ${associationCode}: ${error.message}`);
            failedLogos++;
            return null;
        }
    }
}

/**
 * Traiter une association individuelle
 */
async function processAssociation(association) {
    const { IdAssociation, Name, Code, LogoUrl } = association;
    
    if (!LogoUrl || !Code) {
        console.log(`⚠️  Association ${Code || IdAssociation} sans logo ou code, ignorée`);
        skippedLogos++;
        return;
    }
    
    const outputPath = path.join(CONFIG.OUTPUT_DIR, Code.toUpperCase());
    const existingFiles = CONFIG.LOGO_EXTENSIONS
        .map(ext => path.join(CONFIG.OUTPUT_DIR, Code.toUpperCase() + ext))
        .filter(filePath => fs.existsSync(filePath));
    
    // Vérifier si le logo existe déjà et s'il est récent
    if (existingFiles.length > 0) {
        const stats = fs.statSync(existingFiles[0]);
        const fileAge = Date.now() - stats.mtime.getTime();
        const maxAge = 7 * 24 * 60 * 60 * 1000; // 7 jours
        
        if (fileAge < maxAge) {
            console.log(`⏭️  Logo ${Code} déjà présent et récent, ignoré`);
            skippedLogos++;
            return;
        } else {
            console.log(`🔄 Logo ${Code} existant mais ancien, mise à jour...`);
            // Supprimer l'ancien fichier
            existingFiles.forEach(file => fs.unlinkSync(file));
        }
    }
    
    // Télécharger le nouveau logo
    await downloadLogo(LogoUrl, outputPath, Code);
}

/**
 * Traiter les associations par lots pour éviter la surcharge
 */
async function processAssociationsBatch(associations, batchSize = CONFIG.CONCURRENT_DOWNLOADS) {
    for (let i = 0; i < associations.length; i += batchSize) {
        const batch = associations.slice(i, i + batchSize);
        const promises = batch.map(processAssociation);
        
        console.log(`📦 Traitement du lot ${Math.floor(i / batchSize) + 1}/${Math.ceil(associations.length / batchSize)}`);
        await Promise.all(promises);
        
        // Petite pause entre les lots
        if (i + batchSize < associations.length) {
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
    }
}

/**
 * Fonction principale
 */
async function main() {
    console.log('🏆 MISE À JOUR DES LOGOS FIFA POUR FIT');
    console.log('=====================================');
    console.log('');
    
    try {
        // Vérifier le répertoire de sortie
        ensureOutputDirectory();
        
        console.log('🌐 Récupération de la liste des associations FIFA...');
        
        // Récupérer la liste des associations
        const response = await makeRequest(CONFIG.FIFA_API_URL);
        const associations = JSON.parse(response.data);
        
        if (!Array.isArray(associations)) {
            throw new Error('Format de réponse API invalide');
        }
        
        totalAssociations = associations.length;
        console.log(`✅ ${totalAssociations} associations récupérées depuis l'API FIFA`);
        
        // Filtrer les associations avec des logos
        const associationsWithLogos = associations.filter(assoc => assoc.LogoUrl && assoc.Code);
        console.log(`🎨 ${associationsWithLogos.length} associations avec logos disponibles`);
        
        if (associationsWithLogos.length === 0) {
            console.log('⚠️  Aucune association avec logo trouvée');
            return;
        }
        
        // Traiter les associations par lots
        console.log('⬇️  Début du téléchargement des logos...');
        await processAssociationsBatch(associationsWithLogos);
        
        // Créer un fichier de métadonnées
        const metadata = {
            lastUpdate: new Date().toISOString(),
            totalAssociations: totalAssociations,
            processedAssociations: associationsWithLogos.length,
            downloadedLogos,
            failedLogos,
            skippedLogos,
            associations: associationsWithLogos.map(assoc => ({
                code: assoc.Code,
                name: assoc.Name,
                logoUrl: assoc.LogoUrl
            }))
        };
        
        const metadataPath = path.join(CONFIG.OUTPUT_DIR, 'fifa-logos-metadata.json');
        fs.writeFileSync(metadataPath, JSON.stringify(metadata, null, 2));
        
        console.log('');
        console.log('🎉 MISE À JOUR TERMINÉE !');
        console.log('========================');
        console.log(`📊 Statistiques :`);
        console.log(`   • Associations totales : ${totalAssociations}`);
        console.log(`   • Logos téléchargés : ${downloadedLogos}`);
        console.log(`   • Logos échoués : ${failedLogos}`);
        console.log(`   • Logos ignorés : ${skippedLogos}`);
        console.log(`   • Métadonnées : ${metadataPath}`);
        
        if (failedLogos > 0) {
            console.log('');
            console.log('⚠️  Certains logos n\'ont pas pu être téléchargés.');
            console.log('   Vérifiez la connectivité et relancez le script si nécessaire.');
        }
        
    } catch (error) {
        console.error('❌ ERREUR CRITIQUE :', error.message);
        console.error('Stack trace:', error.stack);
        process.exit(1);
    }
}

// Gestion des signaux d'arrêt
process.on('SIGINT', () => {
    console.log('\n⚠️  Arrêt demandé par l\'utilisateur');
    console.log('📊 Résumé partiel :');
    console.log(`   • Logos téléchargés : ${downloadedLogos}`);
    console.log(`   • Logos échoués : ${failedLogos}`);
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\n⚠️  Arrêt demandé par le système');
    process.exit(0);
});

// Lancer le script
main().catch(error => {
    console.error('❌ Erreur fatale :', error);
    process.exit(1);
});
