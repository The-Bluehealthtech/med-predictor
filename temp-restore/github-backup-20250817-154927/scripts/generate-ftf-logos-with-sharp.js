#!/usr/bin/env node

/**
 * 🎨 Script de génération de vrais logos PNG pour les clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Utilise Sharp pour créer de vrais logos PNG
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import sharp from 'sharp';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration des clubs FTF avec leurs couleurs
const FTF_CLUBS = {
    'EST': {
        name: 'Esperance Sportive de Tunis',
        colors: ['#FF0000', '#FFFFFF', '#000000'],
        city: 'Tunis'
    },
    'ESS': {
        name: 'Etoile Sportive du Sahel',
        colors: ['#00FF00', '#FFFFFF', '#000000'],
        city: 'Sousse'
    },
    'CA': {
        name: 'Club Africain',
        colors: ['#0000FF', '#FFFFFF', '#000000'],
        city: 'Tunis'
    },
    'CSS': {
        name: 'CS Sfaxien',
        colors: ['#FFFF00', '#000000', '#FFFFFF'],
        city: 'Sfax'
    },
    'CAB': {
        name: 'CA Bizertin',
        colors: ['#800080', '#FFFFFF', '#000000'],
        city: 'Bizerte'
    },
    'ST': {
        name: 'Stade Tunisien',
        colors: ['#4B0082', '#FFFFFF', '#000000'],
        city: 'Tunis'
    },
    'USM': {
        name: 'US Monastirienne',
        colors: ['#FF69B4', '#FFFFFF', '#000000'],
        city: 'Monastir'
    },
    'USBG': {
        name: 'US Ben Guerdane',
        colors: ['#FFA500', '#FFFFFF', '#000000'],
        city: 'Ben Guerdane'
    },
    'OB': {
        name: 'Olympique de Béja',
        colors: ['#008080', '#FFFFFF', '#000000'],
        city: 'Béja'
    },
    'ASG': {
        name: 'Avenir Sportif de Gabès',
        colors: ['#00FFFF', '#000000', '#FFFFFF'],
        city: 'Gabès'
    },
    'ESM': {
        name: 'ES de Métlaoui',
        colors: ['#32CD32', '#FFFFFF', '#000000'],
        city: 'Métlaoui'
    },
    'ESZ': {
        name: 'ES de Zarzis',
        colors: ['#FFD700', '#000000', '#FFFFFF'],
        city: 'Zarzis'
    },
    'JSO': {
        name: 'JS de el Omrane',
        colors: ['#00FF7F', '#000000', '#FFFFFF'],
        city: 'El Omrane'
    },
    'EGSG': {
        name: 'El Gawafel de Gafsa',
        colors: ['#FF1493', '#FFFFFF', '#000000'],
        city: 'Gafsa'
    },
    'ASS': {
        name: 'AS Soliman',
        colors: ['#708090', '#FFFFFF', '#000000'],
        city: 'Soliman'
    },
    'UST': {
        name: 'US Tataouine',
        colors: ['#A0522D', '#FFFFFF', '#000000'],
        city: 'Tataouine'
    }
};

// Configuration
const CONFIG = {
    OUTPUT_DIR: path.join(__dirname, '../public/clubs'),
    LOGO_SIZE: 200,
    FONT_SIZE: 24
};

// Créer le dossier de sortie s'il n'existe pas
if (!fs.existsSync(CONFIG.OUTPUT_DIR)) {
    fs.mkdirSync(CONFIG.OUTPUT_DIR, { recursive: true });
    console.log(`📁 Dossier créé : ${CONFIG.OUTPUT_DIR}`);
}

/**
 * Créer un logo PNG avec Sharp
 */
async function createClubLogoWithSharp(code, club) {
    const { name, colors, city } = club;
    const [primaryColor, secondaryColor, textColor] = colors;
    
    try {
        // Créer une image SVG en mémoire
        const svgContent = `
            <svg width="${CONFIG.LOGO_SIZE}" height="${CONFIG.LOGO_SIZE}" viewBox="0 0 ${CONFIG.LOGO_SIZE} ${CONFIG.LOGO_SIZE}" xmlns="http://www.w3.org/2000/svg">
                <!-- Fond principal -->
                <rect width="${CONFIG.LOGO_SIZE}" height="${CONFIG.LOGO_SIZE}" fill="${primaryColor}" rx="20" ry="20"/>
                
                <!-- Bordure -->
                <rect width="${CONFIG.LOGO_SIZE}" height="${CONFIG.LOGO_SIZE}" fill="none" stroke="${textColor}" stroke-width="4" rx="20" ry="20"/>
                
                <!-- Cercle central -->
                <circle cx="${CONFIG.LOGO_SIZE/2}" cy="${CONFIG.LOGO_SIZE/2}" r="60" fill="${secondaryColor}" stroke="${textColor}" stroke-width="3"/>
                
                <!-- Code du club -->
                <text x="${CONFIG.LOGO_SIZE/2}" y="${CONFIG.LOGO_SIZE/2 + 8}" font-family="Arial, sans-serif" font-size="32" font-weight="bold" text-anchor="middle" fill="${textColor}">${code}</text>
                
                <!-- Nom du club (abrévié) -->
                <text x="${CONFIG.LOGO_SIZE/2}" y="${CONFIG.LOGO_SIZE/2 + 50}" font-family="Arial, sans-serif" font-size="16" font-weight="bold" text-anchor="middle" fill="${textColor}">${name.split(' ').slice(0, 2).join(' ')}</text>
                
                <!-- Ville -->
                <text x="${CONFIG.LOGO_SIZE/2}" y="${CONFIG.LOGO_SIZE/2 + 70}" font-family="Arial, sans-serif" font-size="14" text-anchor="middle" fill="${textColor}">${city}</text>
                
                <!-- Étoile FTF -->
                <polygon points="${CONFIG.LOGO_SIZE/2},30 ${CONFIG.LOGO_SIZE/2 + 8},40 ${CONFIG.LOGO_SIZE/2 + 4},50 ${CONFIG.LOGO_SIZE/2 + 8},60 ${CONFIG.LOGO_SIZE/2},70 ${CONFIG.LOGO_SIZE/2 - 8},60 ${CONFIG.LOGO_SIZE/2 - 4},50 ${CONFIG.LOGO_SIZE/2 - 8},40" fill="${textColor}"/>
            </svg>
        `;
        
        // Convertir SVG en PNG avec Sharp
        const pngBuffer = await sharp(Buffer.from(svgContent))
            .png()
            .toBuffer();
        
        return pngBuffer;
        
    } catch (error) {
        console.error(`Erreur lors de la création du logo pour ${code}:`, error.message);
        return null;
    }
}

/**
 * Générer tous les logos des clubs FTF
 */
async function generateAllClubLogos() {
    console.log('🎨 GÉNÉRATION DES VRAIS LOGOS PNG DES CLUBS FTF');
    console.log('================================================');
    console.log('🔧 Utilisation de Sharp pour des PNG de qualité');
    console.log('');
    
    const results = {
        success: [],
        failed: [],
        total: Object.keys(FTF_CLUBS).length
    };
    
    for (const [code, club] of Object.entries(FTF_CLUBS)) {
        console.log(`🏟️ Génération du logo de ${club.name} (${code})...`);
        console.log(`   📍 Ville : ${club.city}`);
        console.log(`   🎨 Couleurs : ${club.colors.join(', ')}`);
        
        try {
            const filename = `${code}.png`;
            const filepath = path.join(CONFIG.OUTPUT_DIR, filename);
            
            // Créer le logo PNG avec Sharp
            const pngBuffer = await createClubLogoWithSharp(code, club);
            
            if (pngBuffer) {
                // Sauvegarder le fichier PNG
                fs.writeFileSync(filepath, pngBuffer);
                
                // Obtenir la taille du fichier
                const stats = fs.statSync(filepath);
                const fileSizeKB = (stats.size / 1024).toFixed(1);
                
                console.log(`✅ ${club.name} (${code}) - Logo PNG généré`);
                console.log(`   📁 PNG : ${filename} (${fileSizeKB} KB)`);
                
                results.success.push({ 
                    code, 
                    name: club.name, 
                    city: club.city,
                    file: filename,
                    size: fileSizeKB
                });
            } else {
                throw new Error('Échec de la génération du logo');
            }
            
        } catch (error) {
            console.log(`❌ ${club.name} (${code}) - Erreur : ${error.message}`);
            results.failed.push({ code, name: club.name, error: error.message, city: club.city });
        }
        
        console.log('');
    }
    
    // Résumé
    console.log('📊 RÉSUMÉ DE LA GÉNÉRATION');
    console.log('============================');
    console.log(`Total des clubs FTF : ${results.total}`);
    console.log(`✅ Succès : ${results.success.length}`);
    console.log(`❌ Échecs : ${results.failed.length}`);
    console.log('');
    
    if (results.success.length > 0) {
        console.log('🏆 LOGOS GÉNÉRÉS AVEC SUCCÈS :');
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
    console.log('🎉 Sharp a généré de vrais fichiers PNG de qualité !');
    
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

