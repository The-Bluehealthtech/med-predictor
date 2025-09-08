-- Initialisation de la base de données FIT
CREATE DATABASE IF NOT EXISTS fit_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utilisateur FIT avec privilèges
CREATE USER IF NOT EXISTS 'fit_user'@'%' IDENTIFIED BY 'fit_password';
GRANT ALL PRIVILEGES ON fit_database.* TO 'fit_user'@'%';
FLUSH PRIVILEGES;

-- Sélection de la base de données
USE fit_database;

-- Création des tables de base (optionnel, Laravel les créera via migrations)
-- Ces tables seront créées automatiquement par les migrations Laravel
