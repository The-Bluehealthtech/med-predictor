#!/usr/bin/env node

/**
 * 🔍 Script de vérification des logos FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Vérifie tous les logos disponibles et identifie les bons
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const CONFIG = {
    CLUBS_DIR: path.join(__dirname, '../public/clubs'),
    BASE_URL: 'http://localhost:8001'
};

// Liste des clubs FTF avec leurs codes attendus
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
    'JSO': 'Jeunesse Sportive de el Omrane',
    'EGSG': 'El Gawafel Sportives de Gafsa',
    'ASS': 'AS Soliman',
    'UST': 'US Tataouine',
    'JSK': 'Jeunesse Sportive de Kairouan' // À vérifier
};

/**
 * Vérifier les logos existants
 */
function checkExistingLogos() {
    console.log('🔍 VÉRIFICATION DES LOGOS EXISTANTS\n');
    console.log('=====================================\n');
    
    if (!fs.existsSync(CONFIG.CLUBS_DIR)) {
        console.log('❌ Dossier des clubs non trouvé');
        return;
    }
    
    const files = fs.readdirSync(CONFIG.CLUBS_DIR);
    const webpFiles = files.filter(file => file.endsWith('.webp'));
    
    console.log(`📁 Logos WebP trouvés : ${webpFiles.length}\n`);
    
    webpFiles.forEach(file => {
        const code = file.replace('.webp', '');
        const clubName = FTF_CLUBS[code] || 'Club inconnu';
        const filePath = path.join(CONFIG.CLUBS_DIR, file);
        const stats = fs.statSync(filePath);
        const sizeKB = Math.round(stats.size / 1024 * 100) / 100;
        
        console.log(`  • ${file} (${sizeKB} KB) → ${clubName}`);
    });
    
    console.log('\n📊 ANALYSE :\n');
    
    // Vérifier les doublons
    const sizes = {};
    webpFiles.forEach(file => {
        const filePath = path.join(CONFIG.CLUBS_DIR, file);
        const stats = fs.statSync(filePath);
        const size = stats.size;
        
        if (!sizes[size]) {
            sizes[size] = [];
        }
        sizes[size].push(file);
    });
    
    Object.entries(sizes).forEach(([size, files]) => {
        if (files.length > 1) {
            console.log(`⚠️  Doublons détectés (${Math.round(size/1024*100)/100} KB) :`);
            files.forEach(file => console.log(`    - ${file}`));
            console.log('');
        }
    });
}

/**
 * Vérifier l'accessibilité web
 */
async function checkWebAccessibility() {
    console.log('🌐 VÉRIFICATION DE L\'ACCESSIBILITÉ WEB\n');
    console.log('======================================\n');
    
    const files = fs.readdirSync(CONFIG.CLUBS_DIR);
    const webpFiles = files.filter(file => file.endsWith('.webp'));
    
    for (const file of webpFiles) {
        const url = `${CONFIG.BASE_URL}/clubs/${file}`;
        console.log(`  • ${file} → ${url}`);
    }
}

/**
 * Recommandations
 */
function showRecommendations() {
    console.log('\n🎯 RECOMMANDATIONS\n');
    console.log('==================\n');
    
    console.log('1. 🔍 Vérifier les URLs des logos sur worldsoccerpins.com');
    console.log('2. 🚫 Supprimer les logos en double');
    console.log('3. ✅ Télécharger les vrais logos manquants');
    console.log('4. 🔧 Corriger le mapping si nécessaire');
    
    console.log('\n📋 PROCHAINES ÉTAPES :\n');
    console.log('• Identifier la vraie URL du logo JS Kairouan');
    console.log('• Télécharger le bon logo JSK');
    console.log('• Vérifier que tous les logos sont uniques');
}

/**
 * Fonction principale
 */
async function main() {
    try {
        console.log('🚀 VÉRIFICATION COMPLÈTE DES LOGOS FTF\n');
        console.log('======================================\n');
        
        checkExistingLogos();
        await checkWebAccessibility();
        showRecommendations();
        
    } catch (error) {
        console.error('💥 Erreur :', error.message);
    }
}

// Exécuter le script
main();

