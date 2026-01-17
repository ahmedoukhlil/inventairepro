<?php

/**
 * Script pour tester et corriger le mot de passe MySQL
 * Aide à identifier si le problème vient du mot de passe ou de la configuration
 * 
 * Usage: php test-mysql-password.php
 */

echo "=== TEST ET CORRECTION MOT DE PASSE MySQL ===\n\n";

// Lire le fichier .env
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ ERREUR: Le fichier .env n'existe pas!\n";
    exit(1);
}

$envContent = file_get_contents($envPath);
preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
preg_match('/DB_PORT=(.+)/', $envContent, $portMatch);
preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
preg_match('/DB_PASSWORD=(.+)/', $envContent, $passMatch);

$dbHost = trim($hostMatch[1] ?? '127.0.0.1');
$dbPort = trim($portMatch[1] ?? '3306');
$dbDatabase = trim($dbMatch[1] ?? '');
$dbUsername = trim($userMatch[1] ?? 'root');
$dbPassword = trim($passMatch[1] ?? '');

echo "Configuration actuelle:\n";
echo "  DB_HOST: {$dbHost}\n";
echo "  DB_PORT: {$dbPort}\n";
echo "  DB_DATABASE: {$dbDatabase}\n";
echo "  DB_USERNAME: {$dbUsername}\n";
echo "  DB_PASSWORD (longueur): " . strlen($dbPassword) . " caractères\n";
echo "  DB_PASSWORD (premiers chars): " . substr($dbPassword, 0, 3) . "***\n\n";

// Afficher le contenu brut de la ligne DB_PASSWORD pour debug
$lines = file($envPath);
foreach ($lines as $num => $line) {
    if (preg_match('/^DB_PASSWORD/', $line)) {
        echo "Ligne DB_PASSWORD dans .env (ligne " . ($num + 1) . "):\n";
        echo "  " . rtrim($line) . "\n";
        echo "  (longueur: " . strlen(trim($line)) . " caractères)\n\n";
        break;
    }
}

// Test 1: Essayer de se connecter avec le mot de passe actuel
echo "=== TEST 1: Connexion avec mot de passe actuel ===\n";
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Connexion réussie avec le mot de passe actuel!\n";
    echo "Le problème doit venir d'ailleurs (cache Laravel, etc.)\n\n";
    exit(0);
} catch (PDOException $e) {
    echo "❌ Échec: " . $e->getMessage() . "\n\n";
}

// Test 2: Demander à l'utilisateur de tester manuellement
echo "=== TEST 2: Vérification manuelle ===\n";
echo "Testez manuellement la connexion MySQL:\n";
echo "  mysql -u {$dbUsername} -p\n\n";
echo "Si la connexion fonctionne avec le mot de passe que vous entrez,\n";
echo "alors le problème vient du fichier .env.\n\n";

// Test 3: Proposer de créer un nouvel utilisateur
echo "=== SOLUTION RECOMMANDÉE ===\n";
echo "Créer un nouvel utilisateur MySQL dédié pour l'application:\n\n";
echo "1. Connectez-vous à MySQL:\n";
echo "   mysql -u root -p\n\n";

echo "2. Créez un nouvel utilisateur (remplacez 'votre_mot_de_passe' par un mot de passe sécurisé):\n";
echo "   CREATE USER '{$dbDatabase}_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';\n";
echo "   GRANT ALL PRIVILEGES ON {$dbDatabase}.* TO '{$dbDatabase}_user'@'localhost';\n";
echo "   FLUSH PRIVILEGES;\n";
echo "   EXIT;\n\n";

echo "3. Mettez à jour le fichier .env:\n";
echo "   nano /var/www/inventairepro/.env\n\n";
echo "   Changez:\n";
echo "   DB_USERNAME={$dbDatabase}_user\n";
echo "   DB_PASSWORD=votre_mot_de_passe\n\n";

echo "4. Nettoyez le cache Laravel:\n";
echo "   cd /var/www/inventairepro\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n\n";

// Test 4: Vérifier si le problème vient des caractères spéciaux
echo "=== DIAGNOSTIC AVANCÉ ===\n";
if (!empty($dbPassword)) {
    // Vérifier les caractères spéciaux
    $specialChars = ['$', '#', '!', '@', '%', '^', '&', '*', '(', ')', '[', ']', '{', '}', '|', '\\', '/', '?', '<', '>', ',', '.', ';', ':', '"', "'"];
    $hasSpecial = false;
    foreach ($specialChars as $char) {
        if (str_contains($dbPassword, $char)) {
            $hasSpecial = true;
            break;
        }
    }
    
    if ($hasSpecial) {
        echo "⚠️  Le mot de passe contient des caractères spéciaux.\n";
        echo "   Assurez-vous qu'il n'y a pas de guillemets autour dans .env\n";
        echo "   Format correct: DB_PASSWORD=mon_mot_de_passe\n";
        echo "   Format incorrect: DB_PASSWORD=\"mon_mot_de_passe\"\n\n";
    }
    
    // Vérifier les espaces
    if ($dbPassword !== trim($dbPassword)) {
        echo "⚠️  Le mot de passe contient des espaces au début ou à la fin!\n";
        echo "   Supprimez-les dans .env\n\n";
    }
    
    // Afficher la représentation hexadécimale pour debug
    echo "Représentation du mot de passe (pour debug):\n";
    echo "  ASCII: " . $dbPassword . "\n";
    echo "  Hex: " . bin2hex($dbPassword) . "\n";
    echo "  Longueur: " . strlen($dbPassword) . " octets\n\n";
}

echo "=== FIN DU DIAGNOSTIC ===\n";
