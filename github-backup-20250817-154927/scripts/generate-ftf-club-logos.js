#!/usr/bin/env node

/**
 * 🎨 Script de génération de logos de test pour les clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Génère des logos SVG simples pour les clubs FTF
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration des clubs FTF en Ligue 1
const FTF_CLUBS = {
    'EST': {
        name: 'Étoile Sportive du Sahel',
        city: 'Sousse',
        color: '#FF6B35',
        abbreviation: 'EST'
    },
    'CA': {
        name: 'Club Africain',
        city: 'Tunis',
        color: '#D4AF37',
        abbreviation: 'CA'
    },
    'ESS': {
        name: 'Étoile Sportive du Sahel',
        city: 'Sousse',
        color: '#FF6B35',
        abbreviation: 'ESS'
    },
    'USM': {
        name: 'Union Sportive Monastir',
        city: 'Monastir',
        color: '#2E86AB',
        abbreviation: 'USM'
    },
    'CAB': {
        name: 'Club Athlétique Bizertin',
        city: 'Bizerte',
        color: '#A23B72',
        abbreviation: 'CAB'
    },
    'ST': {
        name: 'Stade Tunisien',
        city: 'Tunis',
        color: '#F18F01',
        abbreviation: 'ST'
    },
    'JSK': {
        name: 'Jeunesse Sportive Kairouanaise',
        city: 'Kairouan',
        color: '#C73E1D',
        abbreviation: 'JSK'
    },
    'USBG': {
        name: 'Union Sportive Ben Guerdane',
        city: 'Ben Guerdane',
        color: '#2E8B57',
        abbreviation: 'USBG'
    },
    'OB': {
        name: 'Olympique de Béja',
        city: 'Béja',
        color: '#8B4513',
        abbreviation: 'OB'
    },
    'ASM': {
        name: 'AS Marsa',
        city: 'Le Marsa',
        color: '#4682B4',
        abbreviation: 'ASM'
    }
};

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/clubs'),
    LOGO_SIZE: 200
};

// Créer le dossier de sortie s'il n'existe pas
if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
    fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
    console.log(`📁 Dossier créé : ${CONFIG.OUTPUT_DIR}`);
}

/**
 * Générer un logo SVG pour un club
 */
function generateClubLogo(club, code) {
    const { name, city, color, abbreviation } = club;
    
    const svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg width="${CONFIG.LOGO_SIZE}" height="${CONFIG.LOGO_SIZE}" viewBox="0 0 ${CONFIG.LOGO_SIZE} ${CONFIG.LOGO_SIZE}" xmlns="http://www.w3.org/2000/svg">
    <!-- Fond circulaire -->
    <circle cx="${CONFIG.LOGO_SIZE/2}" cy="${CONFIG.LOGO_SIZE/2}" r="${CONFIG.LOGO_SIZE/2 - 10}" fill="${color}" stroke="#FFFFFF" stroke-width="4"/>
    
    <!-- Abréviation du club -->
    <text x="${CONFIG.LOGO_SIZE/2}" y="${CONFIG.LOGO_SIZE/2 + 20}" font-family="Arial, sans-serif" font-size="48" font-weight="bold" text-anchor="middle" fill="#FFFFFF">${abbreviation}</text>
    
    <!-- Nom de la ville -->
    <text x="${CONFIG.LOGO_SIZE/2}" y="${CONFIG.LOGO_SIZE/2 + 60}" font-family="Arial, sans-serif" font-size="16" text-anchor="middle" fill="#FFFFFF">${city}</text>
    
    <!-- Étoile pour l'Étoile Sportive -->
    ${abbreviation === 'EST' || abbreviation === 'ESS' ? `
    <polygon points="${CONFIG.LOGO_SIZE/2},${CONFIG.LOGO_SIZE/2 - 80} ${CONFIG.LOGO_SIZE/2 + 15},${CONFIG.LOGO_SIZE/2 - 50} ${CONFIG.LOGO_SIZE/2 + 25},${CONFIG.LOGO_SIZE/2 - 80} ${CONFIG.LOGO_SIZE/2 + 35},${CONFIG.LOGO_SIZE/2 - 50} ${CONFIG.LOGO_SIZE/2 + 15},${CONFIG.LOGO_SIZE/2 - 20} ${CONFIG.LOGO_SIZE/2 - 15},${CONFIG.LOGO_SIZE/2 - 50} ${CONFIG.LOGO_SIZE/2 - 25},${CONFIG.LOGO_SIZE/2 - 80} ${CONFIG.LOGO_SIZE/2 - 35},${CONFIG.LOGO_SIZE/2 - 50} ${CONFIG.LOGO_SIZE/2 - 15},${CONFIG.LOGO_SIZE/2 - 20}" fill="#FFD700"/>
    ` : ''}
    
    <!-- Ballon de football stylisé -->
    <circle cx="${CONFIG.LOGO_SIZE/2 + 60}" cy="${CONFIG.LOGO_SIZE/2 - 60}" r="15" fill="#FFFFFF" opacity="0.3"/>
    <path d="M ${CONFIG.LOGO_SIZE/2 + 60} ${CONFIG.LOGO_SIZE/2 - 75} Q ${CONFIG.LOGO_SIZE/2 + 75} ${CONFIG.LOGO_SIZE/2 - 60} ${CONFIG.LOGO_SIZE/2 + 60} ${CONFIG.LOGO_SIZE/2 - 45}" stroke="#FFFFFF" stroke-width="2" fill="none" opacity="0.3"/>
</svg>`;
    
    return svg;
}

/**
 * Générer tous les logos des clubs FTF
 */
function generateAllClubLogos() {
    console.log('🎨 GÉNÉRATION DES LOGOS DES CLUBS FTF - LIGUE 1');
    console.log('================================================');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS)) {
        console.log(`🏟️ Génération du logo de ${club.name} (${code})...`);
        
        try {
            const filename = `${code}.svg`;
            const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
            
            const svgContent = generateClubLogo(club, code);
            fs.writeFileSync(filepath, svgContent);
            
            // Vérifier que le fichier a été créé
            if (fs.existsSync(filepath) && fs.statSync(filepath).size > 0) {
                const stats = fs.statSync(filepath);
                const sizeKB = (stats.size / 1024).toFixed(1);
                
                console.log(`✅ ${club.name} (${code}) - ${sizeKB} KB`);
                results.success.push({ code, name: club.name, size: sizeKB });
            } else {
                throw new Error('Fichier vide ou non créé');
            }
            
        } catch (error) {
            console.log(`❌ ${club.name} (${code}) - Erreur : ${error.message}`);
            results.failed.push({ code, name: club.name, error: error.message });
        }
        
        console.log('');
    }
    
    // Résumé
    console.log('📊 RÉSUMÉ DE LA GÉNÉRATION');
    console.log('============================');
    console.log(`Total des clubs : ${results.total}`);
    console.log(`✅ Succès : ${results.success.length}`);
    console.log(`❌ Échecs : ${results.failed.length}`);
    console.log('');
    
    if (results.success.length > 0) {
        console.log('🏆 LOGOS GÉNÉRÉS AVEC SUCCÈS :');
        results.success.forEach(result => {
            console.log(`  • ${result.code}.svg - ${result.name} (${result.size} KB)`);
        });
        console.log('');
    }
    
    if (results.failed.length > 0) {
        console.log('❌ LOGOS EN ÉCHEC :');
        results.failed.forEach(result => {
            console.log(`  • ${result.code} - ${result.name} : ${result.error}`);
        });
        console.log('');
    }
    
    console.log(`📁 Logos générés dans : ${CONFIG.OUTPUT_DIR}`);
    console.log('🎯 Utilisez le composant <x-club-logo-working> dans vos vues !');
    console.log('💡 Les logos sont au format SVG pour une qualité optimale');
    
    return results;
}

// Exécution du script
if (import.meta.url === `file://${process.argv[1]}`) {
    generateAllClubLogos()
        .then(() => {
            console.log('🎉 Script terminé avec succès !');
            process.exit(0);
        })
        .catch((error) => {
            console.error('💥 Erreur fatale :', error);
            process.exit(1);
        });
}

export { generateAllClubLogos, FTF_CLUBS };

