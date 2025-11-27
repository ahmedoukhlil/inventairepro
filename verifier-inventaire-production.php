<?php

/**
 * Script de vérification pour la production
 * À exécuter sur le serveur de production
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventaire;
use App\Models\InventaireLocalisation;

echo "========================================\n";
echo "  VÉRIFICATION INVENTAIRE PRODUCTION\n";
echo "========================================\n\n";

// Afficher tous les inventaires
echo "=== TOUS LES INVENTAIRES ===\n";
$tous = Inventaire::orderBy('created_at', 'desc')->get();

if ($tous->isEmpty()) {
    echo "❌ Aucun inventaire en base\n";
    exit(1);
}

foreach ($tous as $inv) {
    $nbLocalisations = InventaireLocalisation::where('inventaire_id', $inv->id)->count();
    $statutValide = in_array($inv->statut, ['en_cours', 'en_preparation']) ? '✅' : '❌';
    
    echo sprintf(
        "%s ID: %d | Année: %d | Statut: %-15s | Localisations: %d\n",
        $statutValide,
        $inv->id,
        $inv->annee,
        $inv->statut,
        $nbLocalisations
    );
}

echo "\n";

// Vérifier l'inventaire ID 2 spécifiquement
echo "=== VÉRIFICATION INVENTAIRE ID 2 ===\n";
$inventaire2 = Inventaire::find(2);

if ($inventaire2) {
    echo "✅ Inventaire ID 2 trouvé\n";
    echo "   Année: {$inventaire2->annee}\n";
    echo "   Statut: {$inventaire2->statut}\n";
    echo "   Date début: {$inventaire2->date_debut}\n";
    echo "   Date fin: " . ($inventaire2->date_fin ? $inventaire2->date_fin : 'Non définie') . "\n";
    
    $statutsValides = ['en_cours', 'en_preparation'];
    
    if (in_array($inventaire2->statut, $statutsValides)) {
        echo "\n✅ Le statut '{$inventaire2->statut}' est VALIDE pour les scans\n";
    } else {
        echo "\n❌ PROBLÈME: Le statut '{$inventaire2->statut}' n'est PAS valide\n";
        echo "   Statuts valides: " . implode(', ', $statutsValides) . "\n";
        echo "\n=== CORRECTION ===\n";
        $inventaire2->statut = 'en_cours';
        $inventaire2->save();
        echo "✅ Statut changé en: {$inventaire2->statut}\n";
    }
    
    // Vérifier les assignations
    echo "\n   === ASSIGNATIONS ===\n";
    $assignations = InventaireLocalisation::where('inventaire_id', 2)
        ->with(['localisation'])
        ->get();
    
    echo "   Total : {$assignations->count()}\n";
    foreach ($assignations->take(5) as $assign) {
        $userName = $assign->user_id ? \App\Models\User::find($assign->user_id)->name ?? 'ID: ' . $assign->user_id : 'Non assigné';
        echo sprintf(
            "   📍 %s → %s (%d/%d biens) [%s]\n",
            $assign->localisation->code,
            $userName,
            $assign->nombre_biens_scannes,
            $assign->nombre_biens_attendus,
            $assign->statut
        );
    }
} else {
    echo "❌ Inventaire ID 2 introuvable\n";
    echo "   La PWA charge un inventaire qui n'existe pas en base\n";
}

echo "\n=== INVENTAIRE ACTIF (pour la PWA) ===\n";
$inventaireActif = Inventaire::whereIn('statut', ['en_cours', 'en_preparation'])
    ->orderBy('created_at', 'desc')
    ->first();

if ($inventaireActif) {
    echo "✅ Inventaire actif trouvé\n";
    echo "   ID: {$inventaireActif->id}\n";
    echo "   Année: {$inventaireActif->annee}\n";
    echo "   Statut: {$inventaireActif->statut}\n";
    
    if ($inventaireActif->id != 2) {
        echo "\n⚠️ ATTENTION: La PWA charge l'inventaire ID 2, mais l'inventaire actif est l'ID {$inventaireActif->id}\n";
    }
} else {
    echo "❌ Aucun inventaire actif\n";
}

echo "\n========================================\n";
echo "  FIN\n";
echo "========================================\n";

