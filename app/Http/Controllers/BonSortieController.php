<?php

namespace App\Http\Controllers;

use App\Models\StockSortie;
use Illuminate\Http\Request;

class BonSortieController extends Controller
{
    public function imprimer(StockSortie $sortie)
    {
        $sortie->load(['produit.categorie', 'demandeur', 'createur']);

        return view('stock.bon-sortie', compact('sortie'));
    }
}
