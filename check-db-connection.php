<?php

/**
 * Script de diagnostic pour vérifier la connexion à la base de données
 * À exécuter en production pour identifier le problème de connexion
 * 
 * Usage: php check-db-connection.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC CONNEXION BASE DE DONNÉES ===\n\n";

try {
    // Vérifier les variables d'environnement (sans afficher les mots de passe)
    $dbHost = env('DB_HOST', 'localhost');
    $dbPort = env('DB_PORT', '3306');
    $dbDatabase = env('DB_DATABASE', '');
    $dbUsername = env('DB_USERNAME', 'root');
    
    echo "Configuration détectée:\n";
    echo "  DB_HOST: {$dbHost}\n";
    echo "  DB_PORT: {$dbPort}\n";
    echo "  DB_DATABASE: {$dbDatabase}\n";
    echo "  DB_USERNAME: {$dbUsername}\n";
    echo "  DB_PASSWORD: " . (env('DB_PASSWORD') ? '*** (défini)' : '❌ NON DÉFINI') . "\n\n";
    
    // Vérifier si le fichier .env existe
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) {
        echo "❌ ERREUR: Le fichier .env n'existe pas!\n";
        echo "   Créez-le à partir de .env.example\n\n";
        exit(1);
    }
    echo "✅ Fichier .env trouvé\n\n";
    
    // Essayer de se connecter
    echo "Tentative de connexion...\n";
    $connection = \DB::connection();
    $pdo = $connection->getPdo();
    
    echo "✅ Connexion réussie!\n\n";
    
    // Tester une requête simple
    echo "Test de requête...\n";
    $result = \DB::select('SELECT 1 as test');
    echo "✅ Requête réussie!\n\n";
    
    // Vérifier si la table users existe
    echo "Vérification de la table users...\n";
    $tables = \DB::select("SHOW TABLES LIKE 'users'");
    if (empty($tables)) {
        echo "⚠️  La table 'users' n'existe pas\n";
        echo "   Exécutez: php artisan migrate\n\n";
    } else {
        echo "✅ La table 'users' existe\n\n";
    }
    
    echo "=== CONNEXION OK ===\n";
    
} catch (\PDOException $e) {
    echo "❌ ERREUR DE CONNEXION PDO:\n";
    echo "   Code: " . $e->getCode() . "\n";
    echo "   Message: " . $e->getMessage() . "\n\n";
    
    if ($e->getCode() == 1045) {
        echo "=== DIAGNOSTIC ===\n";
        echo "Erreur 1045 = Accès refusé\n\n";
        echo "Solutions possibles:\n";
        echo "1. Vérifier le mot de passe dans .env (DB_PASSWORD)\n";
        echo "2. Vérifier que l'utilisateur MySQL existe:\n";
        echo "   mysql -u root -p\n";
        echo "   SELECT User, Host FROM mysql.user WHERE User = '{$dbUsername}';\n\n";
        echo "3. Vérifier les permissions de l'utilisateur:\n";
        echo "   GRANT ALL PRIVILEGES ON {$dbDatabase}.* TO '{$dbUsername}'@'localhost';\n";
        echo "   FLUSH PRIVILEGES;\n\n";
        echo "4. Vérifier que le serveur MySQL écoute sur le bon port\n";
        echo "5. Vérifier le fichier de configuration MySQL (my.cnf)\n\n";
    } elseif ($e->getCode() == 2002) {
        echo "=== DIAGNOSTIC ===\n";
        echo "Erreur 2002 = Impossible de se connecter au serveur MySQL\n\n";
        echo "Solutions possibles:\n";
        echo "1. Vérifier que MySQL est démarré: systemctl status mysql\n";
        echo "2. Vérifier DB_HOST dans .env (actuellement: {$dbHost})\n";
        echo "3. Vérifier DB_PORT dans .env (actuellement: {$dbPort})\n";
        echo "4. Tester la connexion: mysql -h {$dbHost} -P {$dbPort} -u {$dbUsername} -p\n\n";
    }
    
    exit(1);
} catch (\Exception $e) {
    echo "❌ ERREUR:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
