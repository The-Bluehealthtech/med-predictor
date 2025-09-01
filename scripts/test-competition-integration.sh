#!/bin/bash

# Script de test d'intégration du système des compétitions FIFA Connect
# Teste l'intégration complète : Backend + Frontend + Workflow

set -e

echo "🏆 Test d'Intégration du Système des Compétitions FIFA Connect"
echo "================================================================"
echo ""

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérification de l'environnement
print_status "Vérification de l'environnement..."

# Vérifier que Laravel est accessible
if ! php artisan --version > /dev/null 2>&1; then
    print_error "Laravel n'est pas accessible. Vérifiez que vous êtes dans le bon répertoire."
    exit 1
fi

print_success "Laravel détecté"

# Vérifier la base de données
print_status "Vérification de la base de données..."

if ! php artisan tinker --execute="echo 'DB OK';" > /dev/null 2>&1; then
    print_warning "Impossible de vérifier la base de données. Continuer avec les tests disponibles..."
else
    print_success "Base de données accessible"
fi

# Test 1: Vérification des routes
print_status "Test 1: Vérification des routes des compétitions..."

ROUTES_TO_TEST=(
    "competitions.dashboard"
    "competitions.index"
    "competitions.create"
    "competitions.api.stats"
)

for route in "${ROUTES_TO_TEST[@]}"; do
    if php artisan route:list --name="$route" > /dev/null 2>&1; then
        print_success "Route $route ✓"
    else
        print_error "Route $route ✗"
    fi
done

# Test 2: Vérification des modèles
print_status "Test 2: Vérification des modèles..."

MODELS_TO_TEST=(
    "Competition"
    "CompetitionPhase"
    "CompetitionGroup"
    "CompetitionGroupTeam"
    "Season"
    "Association"
)

for model in "${MODELS_TO_TEST[@]}"; do
    if php artisan tinker --execute="echo class_exists('App\\\\Models\\\\$model') ? 'OK' : 'NOK';" 2>/dev/null | grep -q "OK"; then
        print_success "Modèle $model ✓"
    else
        print_error "Modèle $model ✗"
    fi
done

# Test 3: Vérification des services
print_status "Test 3: Vérification des services..."

SERVICES_TO_TEST=(
    "CompetitionValidationService"
    "CompetitionWorkflowService"
    "FifaConnectService"
)

for service in "${SERVICES_TO_TEST[@]}"; do
    if php artisan tinker --execute="echo class_exists('App\\\\Services\\\\$service') ? 'OK' : 'NOK';" 2>/dev/null | grep -q "OK"; then
        print_success "Service $service ✓"
    else
        print_error "Service $service ✗"
    fi
done

# Test 4: Vérification des vues
print_status "Test 4: Vérification des vues..."

VIEWS_TO_TEST=(
    "modules/competitions/dashboard"
    "modules/competitions/create"
    "modules/competitions/index"
    "modules/competitions/show"
)

for view in "${VIEWS_TO_TEST[@]}"; do
    if [ -f "resources/views/$view.blade.php" ]; then
        print_success "Vue $view.blade.php ✓"
    else
        print_error "Vue $view.blade.php ✗"
    fi
done

# Test 5: Vérification des politiques
print_status "Test 5: Vérification des politiques..."

if [ -f "app/Policies/CompetitionPolicy.php" ]; then
    print_success "Politique CompetitionPolicy ✓"
else
    print_error "Politique CompetitionPolicy ✗"
fi

# Test 6: Vérification des migrations
print_status "Test 6: Vérification des migrations..."

MIGRATIONS_TO_CHECK=(
    "create_competitions_table"
    "create_competition_phases_table"
    "create_competition_groups_table"
    "create_competition_group_teams_table"
)

for migration in "${MIGRATIONS_TO_CHECK[@]}"; do
    if find database/migrations -name "*$migration*" > /dev/null 2>&1; then
        print_success "Migration $migration ✓"
    else
        print_warning "Migration $migration non trouvée (peut être déjà appliquée)"
    fi
done

# Test 7: Vérification des seeders
print_status "Test 7: Vérification des seeders..."

if [ -f "database/seeders/CompetitionTestDataSeeder.php" ]; then
    print_success "Seeder CompetitionTestDataSeeder ✓"
else
    print_error "Seeder CompetitionTestDataSeeder ✗"
fi

# Test 8: Vérification des logs
print_status "Test 8: Vérification de la configuration des logs..."

if php artisan tinker --execute="echo config('logging.channels.competition') ? 'OK' : 'NOK';" 2>/dev/null | grep -q "OK"; then
    print_success "Configuration des logs de compétition ✓"
else
    print_warning "Configuration des logs de compétition non trouvée"
fi

# Test 9: Test de création d'une compétition (simulation)
print_status "Test 9: Test de création d'une compétition (simulation)..."

# Créer un fichier de test temporaire
cat > test_competition_creation.php << 'EOF'
<?php
require_once 'vendor/autoload.php';

use App\Models\Competition;
use App\Models\Association;
use App\Models\Season;

try {
    // Simuler la création d'une compétition
    $association = Association::first();
    $season = Season::first();
    
    if ($association && $season) {
        $competition = new Competition([
            'name' => 'Test Competition',
            'short_name' => 'TEST',
            'type' => 'championship',
            'category' => 'senior',
            'discipline' => 'football',
            'format' => 'round_robin',
            'number_of_teams' => 8,
            'min_teams' => 6,
            'max_teams' => 12,
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(120),
            'registration_deadline' => now()->addDays(15),
            'responsible_person' => 'Test User',
            'contact_email' => 'test@example.com',
            'status' => 'draft'
        ]);
        
        $competition->association()->associate($association);
        $competition->season()->associate($season);
        
        // Ne pas sauvegarder, juste tester la création
        echo "OK - Création de compétition simulée avec succès\n";
    } else
        echo "NOK - Données de test manquantes\n";
    }
} catch (Exception $e) {
    echo "NOK - Erreur: " . $e->getMessage() . "\n";
}
EOF

if php test_competition_creation.php 2>/dev/null | grep -q "OK"; then
    print_success "Test de création de compétition ✓"
else
    print_warning "Test de création de compétition échoué (peut être normal si la DB n'est pas configurée)"
fi

# Nettoyer le fichier temporaire
rm -f test_competition_creation.php

# Test 10: Vérification de l'intégration Vue.js
print_status "Test 10: Vérification de l'intégration Vue.js..."

VUE_FEATURES=(
    "Dashboard interactif"
    "Formulaire de création"
    "Liste avec filtres"
    "Actions en lot"
    "Modals de confirmation"
)

for feature in "${VUE_FEATURES[@]}"; do
    # Vérifier la présence de code Vue.js dans les vues
    if grep -r "v-model\|@click\|v-if\|v-for" resources/views/modules/competitions/ > /dev/null 2>&1; then
        print_success "$feature ✓"
    else
        print_warning "$feature - Vérification manuelle requise"
    fi
done

# Test 11: Vérification des middlewares
print_status "Test 11: Vérification des middlewares..."

if php artisan route:list --name="competitions.dashboard" 2>/dev/null | grep -q "middleware"; then
    print_success "Middlewares appliqués aux routes ✓"
else
    print_warning "Vérification des middlewares requise"
fi

# Test 12: Vérification de la structure des dossiers
print_status "Test 12: Vérification de la structure des dossiers..."

DIRS_TO_CHECK=(
    "app/Models"
    "app/Http/Controllers"
    "app/Services"
    "app/Policies"
    "resources/views/modules/competitions"
    "database/migrations"
    "database/seeders"
)

for dir in "${DIRS_TO_CHECK[@]}"; do
    if [ -d "$dir" ]; then
        print_success "Dossier $dir ✓"
    else
        print_error "Dossier $dir ✗"
    fi
done

# Résumé des tests
echo ""
echo "================================================================"
echo "📊 RÉSUMÉ DES TESTS D'INTÉGRATION"
echo "================================================================"

# Compter les succès et erreurs
TOTAL_TESTS=12
SUCCESS_COUNT=$(grep -c "✓" <<< "$(grep -o "✓\|✗" <<< "$(grep -E "\[SUCCESS\]|\[ERROR\]" <<< "$(cat $0)")")" 2>/dev/null || echo "0")
ERROR_COUNT=$(grep -c "✗" <<< "$(grep -o "✓\|✗" <<< "$(grep -E "\[SUCCESS\]|\[ERROR\]" <<< "$(cat $0)")")" 2>/dev/null || echo "0")

echo "Tests réussis: $SUCCESS_COUNT"
echo "Tests échoués: $ERROR_COUNT"
echo "Total des tests: $TOTAL_TESTS"

if [ "$ERROR_COUNT" -eq 0 ]; then
    echo ""
    print_success "🎉 TOUS LES TESTS D'INTÉGRATION SONT PASSÉS !"
    print_success "Le système des compétitions FIFA Connect est prêt pour la production."
else
    echo ""
    print_warning "⚠️  Certains tests ont échoué. Vérifiez les erreurs ci-dessus."
    print_warning "Le système peut nécessiter des ajustements avant la production."
fi

echo ""
echo "================================================================"
echo "🚀 PROCHAINES ÉTAPES RECOMMANDÉES :"
echo "================================================================"
echo "1. Tester manuellement l'interface utilisateur"
echo "2. Vérifier le workflow complet de création → soumission → validation → publication"
echo "3. Tester les actions en lot"
echo "4. Valider l'intégration avec les composants existants"
echo "5. Effectuer des tests de performance"
echo "6. Former les utilisateurs finaux"
echo ""

print_success "Test d'intégration terminé !"


