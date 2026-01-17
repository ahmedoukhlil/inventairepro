-- Script SQL pour créer un utilisateur MySQL dédié pour l'application
-- À exécuter après s'être connecté à MySQL: mysql -u root -p < create-db-user.sql
-- OU copier-coller les commandes dans mysql

-- Remplacez 'bdimmos' par le nom de votre base de données si différent
-- Remplacez 'votre_mot_de_passe_securise' par un mot de passe fort

-- Créer l'utilisateur
CREATE USER IF NOT EXISTS 'bdimmos_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';

-- Donner tous les privilèges sur la base de données
GRANT ALL PRIVILEGES ON bdimmos.* TO 'bdimmos_user'@'localhost';

-- Appliquer les changements
FLUSH PRIVILEGES;

-- Vérifier que l'utilisateur a été créé
SELECT User, Host FROM mysql.user WHERE User = 'bdimmos_user';

-- Vérifier les permissions
SHOW GRANTS FOR 'bdimmos_user'@'localhost';
