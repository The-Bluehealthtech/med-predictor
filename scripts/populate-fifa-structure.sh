#!/bin/bash

echo "🌍 Création de la Structure FIFA Complète..."
echo "   - 1 CONFÉDÉRATION : CAF"
echo "   - 1 ASSOCIATION : FTF (Tunisie)"
echo "   - 20 CLUBS : Clubs tunisiens de Ligue 1"
echo "   - 40 JOUEURS : De toutes nationalités"
echo ""

# Vérifier que la table existe
echo "🔍 Vérification de la table player_licenses..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "DESCRIBE fit_database.player_licenses;" | head -5

echo ""
echo "🏗️ Création de la structure FIFA..."

# 1. Créer la confédération CAF
echo "   📍 Création de la CAF..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
INSERT IGNORE INTO confederations (name, code, country, created_at, updated_at) VALUES
('Confédération Africaine de Football', 'CAF', 'Afrique', NOW(), NOW());
"

# 2. Créer l'association FTF (Tunisie)
echo "   🇹🇳 Création de la FTF..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
INSERT IGNORE INTO associations (name, country, confederation_id, created_at, updated_at) VALUES
('Fédération Tunisienne de Football', 'Tunisie', 
 (SELECT id FROM confederations WHERE code = 'CAF'), NOW(), NOW());
"

# 3. Créer 20 clubs tunisiens de Ligue 1
echo "   ⚽ Création des 20 clubs tunisiens..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
INSERT IGNORE INTO clubs (name, city, country, association_id, created_at, updated_at) VALUES
('Espérance de Tunis', 'Tunis', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('Club Africain', 'Tunis', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('Étoile du Sahel', 'Sousse', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('CS Sfaxien', 'Sfax', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('US Monastir', 'Monastir', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('Stade Tunisien', 'Tunis', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('CA Bizertin', 'Bizerte', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('JS Kairouan', 'Kairouan', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('AS Gabès', 'Gabès', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('US Tataouine', 'Tataouine', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('AS Soliman', 'Soliman', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('EO Sidi Bouzid', 'Sidi Bouzid', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('JS Soualem', 'Soualem', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('AS Rejiche', 'Rejiche', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('US Ben Guerdane', 'Ben Guerdane', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('CS Hammam-Lif', 'Hammam-Lif', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('AS Marsa', 'La Marsa', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('Stade Gabèsien', 'Gabès', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('JS Tabarka', 'Tabarka', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW()),
('US Siliana', 'Siliana', 'Tunisie', (SELECT id FROM associations WHERE name = 'Fédération Tunisienne de Football'), NOW(), NOW());
"

# 4. Créer 40 joueurs de toutes nationalités
echo "   👥 Création des 40 joueurs..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
INSERT IGNORE INTO players (first_name, last_name, date_of_birth, nationality, position, club_id, fifa_connect_id, created_at, updated_at) VALUES
-- Joueurs tunisiens
('Youssef', 'Msakni', '1990-10-28', 'Tunisie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Duhail'), 'FIFA2024TUN001', NOW(), NOW()),
('Wahbi', 'Khazri', '1991-02-08', 'Tunisie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Montpellier'), 'FIFA2024TUN002', NOW(), NOW()),
('Ferjani', 'Sassi', '1992-03-18', 'Tunisie', 'Milieu', (SELECT id FROM clubs WHERE name = 'Al-Duhail'), 'FIFA2024TUN003', NOW(), NOW()),
('Aymen', 'Mathlouthi', '1984-09-14', 'Tunisie', 'Gardien', (SELECT id FROM clubs WHERE name = 'Étoile du Sahel'), 'FIFA2024TUN004', NOW(), NOW()),
('Dylan', 'Bronn', '1995-06-19', 'Tunisie', 'Défenseur', (SELECT id FROM clubs WHERE name = 'Salernitana'), 'FIFA2024TUN005', NOW(), NOW()),

-- Joueurs algériens
('Riyad', 'Mahrez', '1991-02-21', 'Algérie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Ahli'), 'FIFA2024ALG001', NOW(), NOW()),
('Ismaël', 'Bennacer', '1997-12-01', 'Algérie', 'Milieu', (SELECT id FROM clubs WHERE name = 'AC Milan'), 'FIFA2024ALG002', NOW(), NOW()),
('Yacine', 'Brahimi', '1990-02-08', 'Algérie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Rayyan'), 'FIFA2024ALG003', NOW(), NOW()),

-- Joueurs marocains
('Achraf', 'Hakimi', '1998-11-04', 'Maroc', 'Défenseur', (SELECT id FROM clubs WHERE name = 'Paris Saint-Germain'), 'FIFA2024MAR001', NOW(), NOW()),
('Hakim', 'Ziyech', '1993-03-19', 'Maroc', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Galatasaray'), 'FIFA2024MAR002', NOW(), NOW()),
('Youssef', 'En-Nesyri', '1997-06-01', 'Maroc', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Sevilla'), 'FIFA2024MAR003', NOW(), NOW()),

-- Joueurs égyptiens
('Mohamed', 'Salah', '1992-06-15', 'Égypte', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Liverpool'), 'FIFA2024EGY001', NOW(), NOW()),
('Mohamed', 'Elneny', '1992-07-11', 'Égypte', 'Milieu', (SELECT id FROM clubs WHERE name = 'Arsenal'), 'FIFA2024EGY002', NOW(), NOW()),

-- Joueurs sénégalais
('Sadio', 'Mané', '1992-04-10', 'Sénégal', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Nassr'), 'FIFA2024SEN001', NOW(), NOW()),
('Kalidou', 'Koulibaly', '1991-06-20', 'Sénégal', 'Défenseur', (SELECT id FROM clubs WHERE name = 'Al-Hilal'), 'FIFA2024SEN002', NOW(), NOW()),
('Édouard', 'Mendy', '1992-03-01', 'Sénégal', 'Gardien', (SELECT id FROM clubs WHERE name = 'Al-Ahli'), 'FIFA2024SEN003', NOW(), NOW()),

-- Joueurs nigérians
('Victor', 'Osimhen', '1998-12-29', 'Nigeria', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Napoli'), 'FIFA2024NGA001', NOW(), NOW()),
('Samuel', 'Chukwueze', '1999-05-22', 'Nigeria', 'Attaquant', (SELECT id FROM clubs WHERE name = 'AC Milan'), 'FIFA2024NGA002', NOW(), NOW()),

-- Joueurs ivoiriens
('Sébastien', 'Haller', '1994-06-22', 'Côte d''Ivoire', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Borussia Dortmund'), 'FIFA2024CIV001', NOW(), NOW()),
('Nicolas', 'Pépé', '1995-05-29', 'Côte d''Ivoire', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Trabzonspor'), 'FIFA2024CIV002', NOW(), NOW()),

-- Joueurs ghanéens
('Thomas', 'Partey', '1993-06-13', 'Ghana', 'Milieu', (SELECT id FROM clubs WHERE name = 'Arsenal'), 'FIFA2024GHA001', NOW(), NOW()),
('Mohammed', 'Kudus', '2000-08-02', 'Ghana', 'Attaquant', (SELECT id FROM clubs WHERE name = 'West Ham'), 'FIFA2024GHA002', NOW(), NOW()),

-- Joueurs camerounais
('André-Frank', 'Zambo Anguissa', '1995-11-16', 'Cameroun', 'Milieu', (SELECT id FROM clubs WHERE name = 'Napoli'), 'FIFA2024CMR001', NOW(), NOW()),
('Vincent', 'Aboubakar', '1992-01-22', 'Cameroun', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Beşiktaş'), 'FIFA2024CMR002', NOW(), NOW()),

-- Joueurs maliens
('Yves', 'Bissouma', '1996-08-30', 'Mali', 'Milieu', (SELECT id FROM clubs WHERE name = 'Tottenham'), 'FIFA2024MLI001', NOW(), NOW()),
('Moussa', 'Dembélé', '1995-07-12', 'Mali', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Atlanta'), 'FIFA2024MLI002', NOW(), NOW()),

-- Joueurs sud-africains
('Percy', 'Tau', '1994-05-13', 'Afrique du Sud', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Ahly'), 'FIFA2024ZAF001', NOW(), NOW()),
('Themba', 'Zwane', '1989-08-03', 'Afrique du Sud', 'Milieu', (SELECT id FROM clubs WHERE name = 'Mamelodi Sundowns'), 'FIFA2024ZAF002', NOW(), NOW()),

-- Joueurs angolais
('Gelson', 'Dala', '1996-07-13', 'Angola', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Wakrah'), 'FIFA2024AGO001', NOW(), NOW()),

-- Joueurs zambiens
('Patson', 'Daka', '1998-10-09', 'Zambie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Leicester'), 'FIFA2024ZMB001', NOW(), NOW()),

-- Joueurs tanzaniens
('Mbwana', 'Samatta', '1992-12-23', 'Tanzanie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Gençlerbirliği'), 'FIFA2024TZA001', NOW(), NOW()),

-- Joueurs ougandais
('Emmanuel', 'Okwi', '1992-12-25', 'Ouganda', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Simba SC'), 'FIFA2024UGA001', NOW(), NOW()),

-- Joueurs kényans
('Victor', 'Wanyama', '1991-06-25', 'Kenya', 'Milieu', (SELECT id FROM clubs WHERE name = 'CF Montréal'), 'FIFA2024KEN001', NOW(), NOW()),

-- Joueurs éthiopiens
('Getaneh', 'Kebede', '1992-05-02', 'Éthiopie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Wadi Degla'), 'FIFA2024ETH001', NOW(), NOW()),

-- Joueurs soudanais
('Saif', 'Tera', '1993-02-06', 'Soudan', 'Milieu', (SELECT id FROM clubs WHERE name = 'Al-Hilal'), 'FIFA2024SDN001', NOW(), NOW()),

-- Joueurs libyens
('Ahmed', 'Al-Maghasi', '1996-01-15', 'Libye', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Ahly Tripoli'), 'FIFA2024LBY001', NOW(), NOW()),

-- Joueurs tchadiens
('Ezechiel', 'N''Douassel', '1988-04-20', 'Tchad', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Ahly'), 'FIFA2024TCD001', NOW(), NOW()),

-- Joueurs centrafricains
('Geoffrey', 'Kondogbia', '1993-02-15', 'République centrafricaine', 'Milieu', (SELECT id FROM clubs WHERE name = 'Marseille'), 'FIFA2024CAF001', NOW(), NOW()),

-- Joueurs congolais
('Cédric', 'Bakambu', '1991-04-11', 'République démocratique du Congo', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Galatasaray'), 'FIFA2024COD001', NOW(), NOW()),

-- Joueurs gabonais
('Pierre-Emerick', 'Aubameyang', '1989-06-18', 'Gabon', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Marseille'), 'FIFA2024GAB001', NOW(), NOW()),

-- Joueurs équato-guinéens
('Emilio', 'Nsue', '1989-09-30', 'Guinée équatoriale', 'Défenseur', (SELECT id FROM clubs WHERE name = 'Intercity'), 'FIFA2024GNQ001', NOW(), NOW()),

-- Joueurs cap-verdiens
('Ryan', 'Mendes', '1990-01-08', 'Cap-Vert', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Fatih Karagümrük'), 'FIFA2024CPV001', NOW(), NOW()),

-- Joueurs mauritaniens
('Aboubakar', 'Kamara', '1995-03-07', 'Mauritanie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Aris Limassol'), 'FIFA2024MRT001', NOW(), NOW()),

-- Joueurs gambiens
('Musa', 'Barrow', '1998-11-14', 'Gambie', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Genoa'), 'FIFA2024GMB001', NOW(), NOW()),

-- Joueurs guinéens
('Naby', 'Keïta', '1995-02-10', 'Guinée', 'Milieu', (SELECT id FROM clubs WHERE name = 'Werder Bremen'), 'FIFA2024GIN001', NOW(), NOW()),

-- Joueurs sierra-léonais
('Kei', 'Kamara', '1984-09-01', 'Sierra Leone', 'Attaquant', (SELECT id FROM clubs WHERE name = 'CF Montréal'), 'FIFA2024SLE001', NOW(), NOW()),

-- Joueurs libériens
('William', 'Jebor', '1991-11-10', 'Libéria', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Al-Faisaly'), 'FIFA2024LBR001', NOW(), NOW()),

-- Joueurs togolais
('Emmanuel', 'Adebayor', '1984-02-26', 'Togo', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Retraité'), 'FIFA2024TGO001', NOW(), NOW()),

-- Joueurs béninois
('Stéphane', 'Sessègnon', '1984-06-01', 'Bénin', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Retraité'), 'FIFA2024BEN001', NOW(), NOW()),

-- Joueurs burkinabés
('Bertrand', 'Traoré', '1995-09-06', 'Burkina Faso', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Aston Villa'), 'FIFA2024BFA001', NOW(), NOW()),

-- Joueurs maliens (supplémentaires)
('Amadou', 'Haidara', '1998-01-31', 'Mali', 'Milieu', (SELECT id FROM clubs WHERE name = 'RB Leipzig'), 'FIFA2024MLI003', NOW(), NOW()),
('Ibrahima', 'Koné', '1999-06-16', 'Mali', 'Attaquant', (SELECT id FROM clubs WHERE name = 'Lorient'), 'FIFA2024MLI004', NOW(), NOW());
"

echo ""
echo "✅ Structure FIFA créée avec succès !"
echo ""
echo "📊 Résumé :"
echo "   - 1 Confédération : CAF"
echo "   - 1 Association : FTF (Tunisie)"
echo "   - 20 Clubs tunisiens de Ligue 1"
echo "   - 40 Joueurs de toutes nationalités africaines"
echo ""
echo "🔍 Vérification des données..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;
SELECT 'Confédérations' as type, COUNT(*) as count FROM confederations
UNION ALL
SELECT 'Associations', COUNT(*) FROM associations
UNION ALL
SELECT 'Clubs', COUNT(*) FROM clubs
UNION ALL
SELECT 'Joueurs', COUNT(*) FROM players;
"


