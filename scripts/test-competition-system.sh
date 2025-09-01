#!/bin/bash

echo "🏆 Test du système des compétitions - Plateforme FIT"
echo "=================================================="

# Vérifier que Laravel est accessible
echo "1. Vérification de l'accessibilité Laravel..."
if php artisan --version > /dev/null 2>&1; then
    echo "   ✅ Laravel accessible"
else
    echo "   ❌ Laravel non accessible"
    exit 1
fi

# Vérifier la base de données
echo "2. Vérification de la base de données..."
if php artisan migrate:status > /dev/null 2>&1; then
    echo "   ✅ Base de données accessible"
else
    echo "   ❌ Base de données non accessible"
    exit 1
fi

# Créer les données de test
echo "3. Création des données de test..."
if php artisan db:seed --class=CompetitionTestDataSeeder > /dev/null 2>&1; then
    echo "   ✅ Données de test créées"
else
    echo "   ❌ Erreur lors de la création des données"
    exit 1
fi

# Vérifier les données créées
echo "4. Vérification des données créées..."

echo "   - Saisons:"
php artisan tinker --execute="echo '   Total: ' . App\Models\Season::count() . PHP_EOL; App\Models\Season::all()->each(function(\$s) { echo '     * ' . \$s->name . ' (' . \$s->short_name . ')' . PHP_EOL; });"

echo "   - Associations:"
php artisan tinker --execute="echo '   Total: ' . App\Models\Association::count() . PHP_EOL; App\Models\Association::all()->each(function(\$a) { echo '     * ' . \$a->name . ' (' . \$a->short_name . ')' . PHP_EOL; });"

echo "   - Compétitions:"
php artisan tinker --execute="echo '   Total: ' . App\Models\Competition::count() . PHP_EOL; App\Models\Competition::with(['association', 'season'])->get()->each(function(\$c) { echo '     * ' . \$c->name . ' - ' . \$c->association->short_name . ' (' . \$c->season->short_name . ')' . PHP_EOL; });"

echo "   - Matchs:"
php artisan tinker --execute="echo '   Total: ' . App\Models\GameMatch::count() . PHP_EOL;"

echo "   - Classements:"
php artisan tinker --execute="echo '   Total: ' . App\Models\Standing::count() . PHP_EOL;"

echo "   - Lineups:"
php artisan tinker --execute="echo '   Total: ' . App\Models\MatchRoster::count() . PHP_EOL;"

# Tester les routes
echo "5. Test des routes..."
if curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/associations" | grep -q "200\|302"; then
    echo "   ✅ Route /associations accessible"
else
    echo "   ❌ Route /associations non accessible"
fi

# Test de la navigation
echo "6. Test de la navigation..."
echo "   📍 Accédez à: http://localhost:8080/associations"
echo "   📍 Cliquez sur le bouton '🏆 Compétitions' d'une association"
echo "   📍 Vous devriez être redirigé vers: /competitions/by-association/{id}"

echo ""
echo "🎯 Tests terminés !"
echo ""
echo "📋 Prochaines étapes:"
echo "   1. Ouvrir http://localhost:8080/associations"
echo "   2. Cliquer sur '🏆 Compétitions' d'une association"
echo "   3. Vérifier l'affichage des compétitions"
echo "   4. Tester les filtres (saison, type, statut)"
echo "   5. Vérifier les liens vers détails, classements, matchs"
echo ""
echo "🔧 En cas de problème:"
echo "   - Vérifier les logs: tail -f storage/logs/laravel.log"
echo "   - Vérifier la base: php artisan tinker"
echo "   - Relancer le serveur: php artisan serve"


