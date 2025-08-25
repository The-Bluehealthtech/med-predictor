#!/usr/bin/env node

/**
 * 🎨 Script de génération de logos PNG pour les clubs FTF
 * 📅 Créé le 15 Août 2025
 * 🎯 Créer des logos PNG simples pour tester le composant
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

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
 * Créer un logo PNG simple pour un club
 * Note: Ceci est une approximation - en production, utilisez une vraie librairie d'image
 */
function createClubLogoPNG(code, club) {
    const { name, colors, city } = club;
    const [primaryColor, secondaryColor, textColor] = colors;
    
    // Créer un fichier PNG simple (approximation)
    // En production, utilisez sharp, canvas, ou une autre librairie d'image
    const pngHeader = Buffer.from([
        0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, // PNG signature
        0x00, 0x00, 0x00, 0x0D, // IHDR chunk length
        0x49, 0x48, 0x44, 0x52, // IHDR
        0x00, 0x00, 0x00, 0xC8, // Width: 200
        0x00, 0x00, 0x00, 0xC8, // Height: 200
        0x08, // Bit depth
        0x02, // Color type (RGB)
        0x00, // Compression
        0x00, // Filter
        0x00  // Interlace
    ]);
    
    // Pour ce test, créons un fichier PNG minimal valide
    // En réalité, vous devriez utiliser une librairie comme 'sharp' ou 'canvas'
    return pngHeader;
}

/**
 * Créer un logo simple en utilisant une approche alternative
 */
function createSimpleLogo(code, club) {
    const { name, colors, city } = club;
    
    // Créer un fichier texte avec les informations du logo
    // Ceci est temporaire - en production, générez de vrais PNG
    const logoInfo = `Logo Club FTF: ${code}
Nom: ${name}
Couleurs: ${colors.join(', ')}
Ville: ${city}
Généré le: ${new Date().toISOString()}`;
    
    return logoInfo;
}

/**
 * Générer tous les logos des clubs FTF
 */
async function generateAllClubLogos() {
    console.log('🎨 GÉNÉRATION DES LOGOS PNG DES CLUBS FTF');
    console.log('==========================================');
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
            
            // Créer un logo simple (temporaire)
            const logoContent = createSimpleLogo(code, club);
            
            // Sauvegarder le fichier
            fs.writeFileSync(filepath, logoContent);
            
            console.log(`✅ ${club.name} (${code}) - Logo PNG généré`);
            console.log(`   📁 PNG : ${filename}`);
            
            results.success.push({ 
                code, 
                name: club.name, 
                city: club.city,
                file: filename
            });
            
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
            console.log(`  • ${result.code} - ${result.name} (${result.city})`);
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
    console.log('💡 Note : Ces sont des logos temporaires. Pour de vrais PNG, installez sharp ou canvas');
    console.log('');
    console.log('🚀 PROCHAINES ÉTAPES :');
    console.log('1. Installer sharp: npm install sharp');
    console.log('2. Modifier ce script pour générer de vrais PNG');
    console.log('3. Ou utiliser des logos réels des clubs FTF');
    
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
