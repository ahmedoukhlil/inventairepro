<?php

/**
 * Script de diagnostic pour vérifier la structure de la table users
 * À exécuter en production pour identifier le problème
 * 
 * Usage: php check-users-table-structure.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DIAGNOSTIC TABLE USERS ===\n\n";

try {
    // Vérifier si la table existe
    $tableExists = DB::select("SHOW TABLES LIKE 'users'");
    if (empty($tableExists)) {
        echo "❌ ERREUR: La table 'users' n'existe pas!\n";
        exit(1);
    }
    echo "✅ La table 'users' existe\n\n";
    
    // Récupérer la structure de la table
    $columns = DB::select("SHOW COLUMNS FROM `users`");
    echo "=== STRUCTURE DE LA TABLE ===\n";
    echo str_pad("Colonne", 20) . " | " . str_pad("Type", 20) . " | " . str_pad("Null", 5) . " | " . "Key\n";
    echo str_repeat("-", 70) . "\n";
    
    $columnNames = [];
    foreach ($columns as $column) {
        $columnNames[] = $column->Field;
        echo str_pad($column->Field, 20) . " | " . 
             str_pad($column->Type, 20) . " | " . 
             str_pad($column->Null, 5) . " | " . 
             $column->Key . "\n";
    }
    
    echo "\n=== ANALYSE ===\n";
    
    // Vérifier la colonne username
    if (in_array('users', $columnNames)) {
        echo "✅ Colonne 'users' trouvée (structure attendue)\n";
    } elseif (in_array('name', $columnNames)) {
        echo "⚠️  Colonne 'name' trouvée au lieu de 'users' (ancienne structure)\n";
        echo "   → La migration de correction doit être exécutée\n";
    } elseif (in_array('email', $columnNames)) {
        echo "⚠️  Colonne 'email' trouvée (structure Laravel standard)\n";
        echo "   → La migration de correction doit être exécutée\n";
    } else {
        echo "❌ Aucune colonne username trouvée (ni 'users', ni 'name', ni 'email)\n";
    }
    
    // Vérifier la colonne password
    if (in_array('mdp', $columnNames)) {
        echo "✅ Colonne 'mdp' trouvée (structure attendue)\n";
    } elseif (in_array('password', $columnNames)) {
        echo "⚠️  Colonne 'password' trouvée au lieu de 'mdp' (ancienne structure)\n";
        echo "   → La migration de correction doit être exécutée\n";
    } else {
        echo "❌ Aucune colonne password trouvée (ni 'mdp', ni 'password')\n";
    }
    
    // Vérifier la clé primaire
    $primaryKey = DB::select("SHOW KEYS FROM `users` WHERE Key_name = 'PRIMARY'");
    if (!empty($primaryKey)) {
        $pkColumn = $primaryKey[0]->Column_name;
        echo "✅ Clé primaire: '{$pkColumn}'\n";
        if ($pkColumn !== 'idUser') {
            echo "   ⚠️  La clé primaire devrait être 'idUser' mais c'est '{$pkColumn}'\n";
        }
    }
    
    // Vérifier la colonne role
    if (in_array('role', $columnNames)) {
        echo "✅ Colonne 'role' trouvée\n";
    } else {
        echo "⚠️  Colonne 'role' manquante\n";
        echo "   → La migration de correction doit être exécutée\n";
    }
    
    echo "\n=== RECOMMANDATIONS ===\n";
    if (!in_array('users', $columnNames) || !in_array('mdp', $columnNames)) {
        echo "1. Exécuter la migration de correction:\n";
        echo "   php artisan migrate --path=database/migrations/2026_01_20_000000_fix_users_table_structure.php\n\n";
    }
    
    echo "2. Vérifier que toutes les migrations sont à jour:\n";
    echo "   php artisan migrate:status\n\n";
    
    echo "3. Si nécessaire, exécuter toutes les migrations:\n";
    echo "   php artisan migrate\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
