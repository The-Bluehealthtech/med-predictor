const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

console.log('🚀 Optimisation de la Landing Page...\n');

// Vérifier que les images existent
const images = [
    'public/images/logos/fit.png',
    'public/images/logos/fifa.png',
    'public/images/logos/the-blue-healthtech-logo.png'
];

console.log('📸 Vérification des images...');
images.forEach(image => {
    if (fs.existsSync(image)) {
        console.log(`✅ ${image}`);
    } else {
        console.log(`❌ ${image} - MANQUANT`);
    }
});

// Vérifier les routes
console.log('\n🛣️  Vérification des routes...');
exec('php artisan route:list --name=landing', (error, stdout, stderr) => {
    if (error) {
        console.log('❌ Erreur lors de la vérification des routes:', error.message);
    } else {
        console.log('✅ Route landing configurée');
        console.log(stdout);
    }
    
    // Vérifier les traductions
    console.log('\n🌐 Vérification des traductions...');
    const translationFiles = [
        'resources/lang/fr/landing.php',
        'resources/lang/en/landing.php'
    ];
    
    translationFiles.forEach(file => {
        if (fs.existsSync(file)) {
            console.log(`✅ ${file}`);
        } else {
            console.log(`❌ ${file} - MANQUANT`);
        }
    });
    
    // Vérifier la vue
    console.log('\n👁️  Vérification de la vue...');
    if (fs.existsSync('resources/views/landing.blade.php')) {
        console.log('✅ resources/views/landing.blade.php');
    } else {
        console.log('❌ resources/views/landing.blade.php - MANQUANT');
    }
    
    console.log('\n🎯 OPTIMISATIONS RECOMMANDÉES:');
    console.log('1. Compresser les images PNG/JPEG');
    console.log('2. Utiliser des formats WebP pour de meilleures performances');
    console.log('3. Implémenter le lazy loading pour les images');
    console.log('4. Ajouter des meta tags SEO');
    console.log('5. Optimiser le CSS avec PurgeCSS');
    console.log('6. Implémenter le service worker pour le cache');
    console.log('7. Ajouter des analytics (Google Analytics, etc.)');
    console.log('8. Tester la performance avec Lighthouse');
    
    console.log('\n🔧 COMMANDES UTILES:');
    console.log('npm run build - Construire les assets');
    console.log('php artisan view:clear - Vider le cache des vues');
    console.log('php artisan config:clear - Vider le cache de config');
    console.log('php artisan cache:clear - Vider le cache général');
    
    console.log('\n✅ Optimisation terminée !');
}); 