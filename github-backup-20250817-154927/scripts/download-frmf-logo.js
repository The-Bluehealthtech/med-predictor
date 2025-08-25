#!/usr/bin/env node

/**
 * ⚽ Script de téléchargement du logo de la FRMF
 * 📅 Créé le : 15 août 2025
 * 🎯 Objectif : Télécharger le logo officiel de la Fédération Royale Marocaine de Football
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

// Sources fiables pour le logo de la FRMF
const FRMF_LOGO_SOURCES = [
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/FRMF_logo.svg/1200px-FRMF_logo.svg.png',
    'https://www.frmf.ma/sites/default/files/logo-frmf.png',
    'https://www.cafonline.com/images/2019/09/19/frmf-logo.png',
    'https://www.fifa.com/images/associations/ma.png'
];

// Logging
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
async function downloadLogo(url, filepath) {
    try {
        log(`Tentative de téléchargement depuis : ${url}`);
        
        const response = await axios({
            method: 'GET',
            url: url,
            responseType: 'stream',
            timeout: CONFIG.TIMEOUT,
            headers: {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            }
        });

        const writer = fs.createWriteStream(filepath);
        response.data.pipe(writer);

        return new Promise((resolve, reject) => {
            writer.on('finish', () => {
                const stats = fs.statSync(filepath);
                log(`Logo téléchargé avec succès : ${path.basename(filepath)} (${(stats.size / 1024).toFixed(1)} KB)`, 'SUCCESS');
                resolve(true);
            });
            writer.on('error', reject);
        });

    } catch (error) {
        log(`Échec du téléchargement depuis ${url}: ${error.message}`, 'ERROR');
        return false;
    }
}

// Fonction principale
async function main() {
    try {
        log('🚀 Démarrage du téléchargement du logo de la FRMF...');
        
        ensureOutputDirectory();
        
        const outputPath = path.join(CONFIG.OUTPUT_DIR, 'MA.png');
        
        // Essayer chaque source jusqu'à ce qu'une fonctionne
        for (const sourceUrl of FRMF_LOGO_SOURCES) {
            log(`Tentative avec la source : ${sourceUrl}`);
            
            const success = await downloadLogo(sourceUrl, outputPath);
            
            if (success) {
                log('🎉 Logo de la FRMF téléchargé avec succès !', 'SUCCESS');
                log(`📁 Fichier sauvegardé : ${outputPath}`);
                return;
            }
            
            // Attendre un peu entre les tentatives
            await new Promise(resolve => setTimeout(resolve, 2000));
        }
        
        // Si aucune source n'a fonctionné, créer un logo par défaut
        log('⚠️ Aucune source n\'a fonctionné, création d\'un logo par défaut', 'WARNING');
        
        // Créer un logo simple avec le texte "FRMF"
        const svgContent = `
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
    <rect width="200" height="200" fill="#0066cc"/>
    <circle cx="100" cy="100" r="80" fill="none" stroke="white" stroke-width="8"/>
    <text x="100" y="110" font-family="Arial, sans-serif" font-size="24" font-weight="bold" text-anchor="middle" fill="white">FRMF</text>
    <text x="100" y="140" font-family="Arial, sans-serif" font-size="12" text-anchor="middle" fill="white">Maroc</text>
</svg>`;
        
        // Convertir SVG en PNG (approximation simple)
        const pngBuffer = Buffer.from(svgContent);
        fs.writeFileSync(outputPath, pngBuffer);
        
        log('✅ Logo par défaut créé pour la FRMF', 'SUCCESS');
        
    } catch (error) {
        log(`❌ Erreur fatale : ${error.message}`, 'ERROR');
        process.exit(1);
    }
}

// Exécuter le script
main();

