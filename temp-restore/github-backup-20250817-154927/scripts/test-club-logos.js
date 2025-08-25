#!/usr/bin/env node

/**
 * 🧪 Script de test rapide pour le composant club-logo-working
 * 📅 Créé le 15 Août 2025
 * 🎯 Test du composant avec différents noms de clubs
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Clubs de test
const TEST_CLUBS = [
    'Esperance Sportive de Tunis',
    'Etoile Sportive du Sahel',
    'Club Africain',
    'CS Sfaxien',
    'Club Athlétique Bizertin',
    'Stade Tunisien',
    'Union Sportive Monastirienne',
    'Union Sportive de Ben Guerdane',
    'Olympique de Béja',
    'Avenir Sportif de Gabès'
];

console.log('🧪 TEST DU COMPOSANT CLUB-LOGO-WORKING');
console.log('=====================================');
console.log('');

// Vérifier que le composant existe
const componentPath = path.join(__dirname, '../resources/views/components/club-logo-working.blade.php');
if (!fs.existsSync(componentPath)) {
    console.log('❌ Composant non trouvé :', componentPath);
    process.exit(1);
}

console.log('✅ Composant trouvé :', componentPath);
console.log('');

// Vérifier que le dossier clubs existe
const clubsDir = path.join(__dirname, '../public/clubs');
if (!fs.existsSync(clubsDir)) {
    console.log('📁 Création du dossier clubs...');
    fs.mkdirSync(clubsDir, { recursive: true });
}

console.log('✅ Dossier clubs :', clubsDir);
console.log('');

// Lister les logos existants
const existingLogos = fs.readdirSync(clubsDir).filter(file => file.endsWith('.png'));
console.log('📊 Logos existants :', existingLogos.length);
if (existingLogos.length > 0) {
    existingLogos.forEach(logo => {
        const stats = fs.statSync(path.join(clubsDir, logo));
        const sizeKB = (stats.size / 1024).toFixed(1);
        console.log(`  • ${logo} - ${sizeKB} KB`);
    });
} else {
    console.log('  Aucun logo trouvé - Utilisation des fallbacks 🏟️');
}
console.log('');

// Test des noms de clubs
console.log('🏟️ TEST DES NOMS DE CLUBS :');
TEST_CLUBS.forEach((clubName, index) => {
    console.log(`${index + 1}. ${clubName}`);
    
    // Simuler la logique du composant
    const clubMapping = {
        'esperance sportive de tunis': 'EST',
        'etoile sportive du sahel': 'ESS',
        'club africain': 'CA',
        'cs sfaxien': 'CSS',
        'club athletique bizertin': 'CAB',
        'stade tunisien': 'ST',
        'union sportive monastirienne': 'USM',
        'union sportive de ben guerdane': 'USBG',
        'olympique de beja': 'OB',
        'avenir sportif de gabes': 'ASG'
    };
    
    let clubCode = null;
    for (const [name, code] of Object.entries(clubMapping)) {
        if (clubName.toLowerCase().includes(name)) {
            clubCode = code;
            break;
        }
    }
    
    if (clubCode) {
        const logoPath = path.join(clubsDir, `${clubCode}.png`);
        if (fs.existsSync(logoPath)) {
            const stats = fs.statSync(logoPath);
            const sizeKB = (stats.size / 1024).toFixed(1);
            console.log(`   ✅ Code: ${clubCode} - Logo: ${clubCode}.png (${sizeKB} KB)`);
        } else {
            console.log(`   ⚠️  Code: ${clubCode} - Logo manquant (fallback 🏟️)`);
        }
    } else {
        console.log(`   ❌ Code non trouvé - Fallback 🏟️`);
    }
});

console.log('');
console.log('🎯 RÉSUMÉ DU TEST :');
console.log('===================');
console.log(`Total des clubs testés : ${TEST_CLUBS.length}`);
console.log(`Logos disponibles : ${existingLogos.length}`);
console.log(`Fallbacks nécessaires : ${TEST_CLUBS.length - existingLogos.length}`);
console.log('');
console.log('🚀 PROCHAINES ÉTAPES :');
console.log('1. Télécharger les logos depuis worldsoccerpins.com');
console.log('2. Tester le composant dans Laravel');
console.log('3. Intégrer dans le portail patient');
console.log('');
console.log('🌐 Source des logos : https://www.worldsoccerpins.com/football-logos-tunisia');
console.log('📁 Script de téléchargement : scripts/download-ftf-club-logos.js');
console.log('�� Test terminé !');

