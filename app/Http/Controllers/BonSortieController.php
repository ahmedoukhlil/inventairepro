<?php

namespace App\Http\Controllers;

use App\Models\StockSortie;
use Illuminate\Http\Request;

class BonSortieController extends Controller
{
    public function imprimer(StockSortie $sortie)
    {
        $sortie->load(['produit.categorie', 'produit.magasin', 'demandeur', 'createur']);

        // Si la sortie fait partie d'un groupe, afficher le bon groupé
        if ($sortie->groupe_id) {
            return $this->imprimerGroupe($sortie->groupe_id);
        }

        // Sortie unique (anciennes sorties sans groupe_id)
        $sorties = collect([$sortie]);
        return view('stock.bon-sortie', [
            'sorties'   => $sorties,
            'demandeur' => $sortie->demandeur,
            'createur'  => $sortie->createur,
            'date'      => $sortie->date_sortie,
            'observations' => $sortie->observations,
        ]);
    }

    public function imprimerGroupe(string $groupeId)
    {
        $sorties = StockSortie::where('groupe_id', $groupeId)
            ->with(['produit.categorie', 'produit.magasin', 'demandeur', 'createur'])
            ->orderBy('id')
            ->get();

        abort_if($sorties->isEmpty(), 404);

        $premiere = $sorties->first();

        return view('stock.bon-sortie', [
            'sorties'      => $sorties,
            'demandeur'    => $premiere->demandeur,
            'createur'     => $premiere->createur,
            'date'         => $premiere->date_sortie,
            'observations' => $premiere->observations,
        ]);
    }
}
