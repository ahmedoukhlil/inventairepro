<?php

/**
 * Script de diagnostic et correction pour la connexion MySQL
 * À exécuter en production pour identifier et résoudre le problème
 * 
 * Usage: php fix-db-connection.php
 */

echo "=== DIAGNOSTIC ET CORRECTION CONNEXION MySQL ===\n\n";

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

echo "Configuration détectée:\n";
echo "  DB_HOST: {$dbHost}\n";
echo "  DB_PORT: {$dbPort}\n";
echo "  DB_DATABASE: {$dbDatabase}\n";
echo "  DB_USERNAME: {$dbUsername}\n";
echo "  DB_PASSWORD: " . ($dbPassword ? '*** (défini, longueur: ' . strlen($dbPassword) . ')' : '❌ NON DÉFINI') . "\n\n";

// Test 1: Essayer de se connecter directement avec PDO
echo "=== TEST 1: Connexion PDO directe ===\n";
try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
    echo "✅ Connexion PDO réussie!\n\n";
} catch (PDOException $e) {
    echo "❌ Échec: " . $e->getMessage() . "\n\n";
    
    // Test 2: Essayer avec socket Unix (si sur localhost)
    if ($dbHost === '127.0.0.1' || $dbHost === 'localhost') {
        echo "=== TEST 2: Connexion via socket Unix ===\n";
        try {
            // Chercher le socket MySQL
            $socketPaths = [
                '/var/run/mysqld/mysqld.sock',
                '/tmp/mysql.sock',
                '/var/lib/mysql/mysql.sock',
                '/run/mysqld/mysqld.sock',
            ];
            
            $socketFound = false;
            foreach ($socketPaths as $socketPath) {
                if (file_exists($socketPath)) {
                    echo "Socket trouvé: {$socketPath}\n";
                    $dsn = "mysql:unix_socket={$socketPath};dbname={$dbDatabase};charset=utf8mb4";
                    try {
                        $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        ]);
                        echo "✅ Connexion via socket réussie!\n\n";
                        echo "=== SOLUTION ===\n";
                        echo "Ajoutez cette ligne dans votre .env:\n";
                        echo "DB_SOCKET={$socketPath}\n\n";
                        $socketFound = true;
                        break;
                    } catch (PDOException $e2) {
                        echo "❌ Échec avec socket: " . $e2->getMessage() . "\n";
                    }
                }
            }
            
            if (!$socketFound) {
                echo "Aucun socket MySQL trouvé dans les emplacements standards.\n\n";
            }
        } catch (Exception $e) {
            echo "❌ Erreur lors de la recherche du socket: " . $e->getMessage() . "\n\n";
        }
    }
    
    // Test 3: Vérifier si le mot de passe contient des caractères spéciaux
    echo "=== TEST 3: Vérification du mot de passe ===\n";
    if (empty($dbPassword)) {
        echo "❌ Le mot de passe est vide dans .env\n";
        echo "   Vérifiez que DB_PASSWORD est bien défini\n\n";
    } else {
        echo "✅ Le mot de passe est défini (longueur: " . strlen($dbPassword) . " caractères)\n";
        
        // Vérifier s'il y a des espaces ou des guillemets
        if (trim($dbPassword) !== $dbPassword) {
            echo "⚠️  ATTENTION: Le mot de passe contient des espaces au début ou à la fin!\n";
            echo "   Supprimez les espaces dans .env\n\n";
        }
        
        // Vérifier les guillemets
        if ((str_starts_with($dbPassword, '"') && str_ends_with($dbPassword, '"')) ||
            (str_starts_with($dbPassword, "'") && str_ends_with($dbPassword, "'"))) {
            echo "⚠️  ATTENTION: Le mot de passe est entre guillemets dans .env!\n";
            echo "   Supprimez les guillemets autour de DB_PASSWORD\n\n";
        }
    }
    
    // Test 4: Essayer de se connecter sans mot de passe (si root)
    if ($dbUsername === 'root' && !empty($dbPassword)) {
        echo "=== TEST 4: Test sans mot de passe (root uniquement) ===\n";
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdo = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            echo "✅ Connexion réussie SANS mot de passe!\n";
            echo "   Cela signifie que le mot de passe dans .env est incorrect\n\n";
        } catch (PDOException $e) {
            echo "❌ Échec aussi sans mot de passe: " . $e->getMessage() . "\n\n";
        }
    }
    
    // Recommandations
    echo "=== RECOMMANDATIONS ===\n\n";
    echo "1. Vérifier le mot de passe MySQL:\n";
    echo "   mysql -u root -p\n";
    echo "   (Entrez le mot de passe que vous utilisez normalement)\n\n";
    
    echo "2. Si la connexion MySQL fonctionne, vérifiez le .env:\n";
    echo "   - Le mot de passe est-il exactement le même?\n";
    echo "   - Y a-t-il des espaces avant/après?\n";
    echo "   - Y a-t-il des guillemets autour?\n\n";
    
    echo "3. Créer un nouvel utilisateur MySQL dédié:\n";
    echo "   mysql -u root -p\n";
    echo "   CREATE USER '{$dbDatabase}_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';\n";
    echo "   GRANT ALL PRIVILEGES ON {$dbDatabase}.* TO '{$dbDatabase}_user'@'localhost';\n";
    echo "   FLUSH PRIVILEGES;\n";
    echo "   Puis mettez à jour .env avec ce nouvel utilisateur\n\n";
    
    echo "4. Vérifier les permissions:\n";
    echo "   mysql -u root -p\n";
    echo "   SELECT User, Host FROM mysql.user WHERE User = '{$dbUsername}';\n";
    echo "   SHOW GRANTS FOR '{$dbUsername}'@'localhost';\n\n";
    
    exit(1);
}

// Si on arrive ici, la connexion fonctionne
echo "=== CONNEXION RÉUSSIE ===\n";
echo "La connexion à la base de données fonctionne correctement.\n";
echo "Le problème doit venir d'ailleurs (cache Laravel, etc.)\n\n";

echo "Nettoyage du cache Laravel...\n";
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    \Artisan::call('config:clear');
    echo "✅ Cache de configuration nettoyé\n";
    
    \Artisan::call('cache:clear');
    echo "✅ Cache applicatif nettoyé\n";
    
    echo "\n✅ Tout est prêt! Essayez de vous connecter maintenant.\n";
} catch (Exception $e) {
    echo "⚠️  Impossible de nettoyer le cache: " . $e->getMessage() . "\n";
    echo "   Exécutez manuellement: php artisan config:clear\n";
}
