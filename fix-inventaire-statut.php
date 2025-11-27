<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventaire;

echo "========================================\n";
echo "  CORRECTION STATUT INVENTAIRE\n";
echo "========================================\n\n";

// Afficher tous les inventaires
echo "=== TOUS LES INVENTAIRES ===\n";
$tous = Inventaire::orderBy('created_at', 'desc')->get();

if ($tous->isEmpty()) {
    echo "❌ Aucun inventaire en base\n";
    exit(1);
}

foreach ($tous as $inv) {
    $nbLocalisations = \App\Models\InventaireLocalisation::where('inventaire_id', $inv->id)->count();
    echo sprintf(
        "ID: %d | Année: %d | Statut: %-15s | Localisations: %d\n",
        $inv->id,
        $inv->annee,
        $inv->statut,
        $nbLocalisations
    );
}

echo "\n";

// Vérifier l'inventaire actif (celui utilisé par la PWA)
$inventaire = Inventaire::whereIn('statut', ['en_cours', 'en_preparation'])
    ->orderBy('created_at', 'desc')
    ->first();

if (!$inventaire) {
    echo "❌ Aucun inventaire actif (en_cours ou en_preparation)\n";
    echo "\n=== CORRECTION: Activer le premier inventaire ===\n";
    $inventaire = Inventaire::orderBy('created_at', 'desc')->first();
    if ($inventaire) {
        echo "Inventaire trouvé: ID {$inventaire->id}, Année {$inventaire->annee}\n";
    } else {
        echo "❌ Aucun inventaire disponible\n";
        exit(1);
    }
}

// Vérifier aussi l'inventaire ID 2 spécifiquement (celui chargé par la PWA)
echo "\n=== VÉRIFICATION INVENTAIRE ID 2 (PWA) ===\n";
$inventaire2 = Inventaire::find(2);
if ($inventaire2) {
    echo "ID: {$inventaire2->id}\n";
    echo "Année: {$inventaire2->annee}\n";
    echo "Statut: {$inventaire2->statut}\n";
    
    if (!in_array($inventaire2->statut, ['en_cours', 'en_preparation'])) {
        echo "❌ PROBLÈME: Le statut '{$inventaire2->statut}' n'est pas valide\n";
        echo "Correction automatique...\n";
        $inventaire2->statut = 'en_cours';
        $inventaire2->save();
        echo "✅ Statut changé en: {$inventaire2->statut}\n";
    } else {
        echo "✅ Le statut '{$inventaire2->statut}' est valide pour les scans\n";
    }
} else {
    echo "⚠️ Inventaire ID 2 n'existe pas en base locale\n";
    echo "   (Il existe peut-être en production)\n";
}

echo "=== ÉTAT ACTUEL ===\n";
echo "ID: {$inventaire->id}\n";
echo "Année: {$inventaire->annee}\n";
echo "Statut: {$inventaire->statut}\n";
echo "Date début: {$inventaire->date_debut}\n";
echo "Date fin: " . ($inventaire->date_fin ? $inventaire->date_fin : 'Non définie') . "\n";
echo "\n";

// Vérifier si le statut est valide pour les scans
$statutsValides = ['en_cours', 'en_preparation'];

if (!in_array($inventaire->statut, $statutsValides)) {
    echo "❌ PROBLÈME: Le statut '{$inventaire->statut}' n'est pas valide pour les scans\n";
    echo "Statuts valides: " . implode(', ', $statutsValides) . "\n\n";
    
    echo "=== CORRECTION ===\n";
    $inventaire->statut = 'en_cours';
    $inventaire->save();
    
    echo "✅ Statut changé en: {$inventaire->statut}\n";
    
    // Vérifier
    $inventaire->refresh();
    echo "✅ Vérification: {$inventaire->statut}\n";
} else {
    echo "✅ Le statut est déjà valide pour les scans\n";
}

echo "\n========================================\n";
echo "  FIN\n";
echo "========================================\n";

