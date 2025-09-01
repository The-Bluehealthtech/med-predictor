#!/bin/bash

echo "🏆 Création de Licences de Test avec les Données Existantes..."

# Créer des licences de test pour les joueurs existants
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Créer des licences de test
INSERT IGNORE INTO licenses (name, type, status, created_at, updated_at) VALUES
('Licence Lionel Messi - Real Madrid', 'professional', 'active', NOW(), NOW()),
('Licence Cristiano Ronaldo - Manchester United', 'professional', 'active', NOW(), NOW()),
('Licence Kylian Mbappé - Paris Saint-Germain', 'professional', 'active', NOW(), NOW()),
('Licence Erling Haaland - Manchester City', 'professional', 'active', NOW(), NOW()),
('Licence Kevin De Bruyne - Liverpool', 'professional', 'active', NOW(), NOW()),
('Licence Neymar - Paris Saint-Germain', 'professional', 'active', NOW(), NOW()),
('Licence Mohamed Salah - Liverpool', 'professional', 'active', NOW(), NOW()),
('Licence Sadio Mané - Bayern Munich', 'professional', 'active', NOW(), NOW()),
('Licence Robert Lewandowski - Barcelona', 'professional', 'active', NOW(), NOW()),
('Licence Karim Benzema - Al-Ittihad', 'professional', 'active', NOW(), NOW());

-- Créer des licences amateurs
INSERT IGNORE INTO licenses (name, type, status, created_at, updated_at) VALUES
('Licence Amateur - Club Local 1', 'amateur', 'active', NOW(), NOW()),
('Licence Amateur - Club Local 2', 'amateur', 'active', NOW(), NOW()),
('Licence Semi-Pro - Club Régional 1', 'semi_pro', 'active', NOW(), NOW()),
('Licence Semi-Pro - Club Régional 2', 'semi_pro', 'active', NOW(), NOW()),
('Licence International - Équipe Nationale', 'international', 'active', NOW(), NOW());

-- Créer des licences en attente
INSERT IGNORE INTO licenses (name, type, status, created_at, updated_at) VALUES
('Licence en Attente - Nouveau Joueur 1', 'amateur', 'pending', NOW(), NOW()),
('Licence en Attente - Nouveau Joueur 2', 'semi_pro', 'pending', NOW(), NOW()),
('Licence en Attente - Transfert', 'professional', 'pending', NOW(), NOW());

-- Créer des licences rejetées
INSERT IGNORE INTO licenses (name, type, status, created_at, updated_at) VALUES
('Licence Rejetée - Documents Manquants', 'professional', 'rejected', NOW(), NOW()),
('Licence Rejetée - Âge Non Conforme', 'amateur', 'rejected', NOW(), NOW());
"

echo "✅ Licences de test créées avec succès !"
echo ""
echo "📊 Résumé des licences créées :"
echo "   - 10 licences professionnelles actives"
echo "   - 5 licences amateurs/semi-pro actives"
echo "   - 3 licences en attente"
echo "   - 2 licences rejetées"
echo ""
echo "🎯 Total: 20 licences créées"
echo ""
echo "🔍 Vérification des licences créées :"

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
SELECT type, status, COUNT(*) as count 
FROM licenses 
GROUP BY type, status 
ORDER BY type, status;
"


