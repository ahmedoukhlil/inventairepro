<?php

namespace App\Http\Controllers;

use App\Models\HistoriqueTransfert;
use Illuminate\Http\Request;

class DecisionTransfertController extends Controller
{
    public function imprimer(string $groupeId)
    {
        $transferts = HistoriqueTransfert::with(['immobilisation.designation', 'utilisateur'])
            ->where('groupe_transfert_id', $groupeId)
            ->orderBy('id')
            ->get();

        abort_if($transferts->isEmpty(), 404);

        $premier = $transferts->first();

        return view('biens.decision-transfert', compact('transferts', 'groupeId', 'premier'));
    }
}
