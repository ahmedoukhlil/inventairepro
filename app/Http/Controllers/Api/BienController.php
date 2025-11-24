<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use Illuminate\Http\Request;

/**
 * Controller API pour la gestion des biens depuis la PWA
 * Gère les opérations de bien via l'API
 */
class BienController extends Controller
{
    /**
     * Récupérer les détails d'un bien
     * Utilisé lors du scan QR code d'un bien
     * 
     * @param Bien $bien
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Bien $bien)
    {
        // Vérifier que le bien n'est pas soft deleted
        if ($bien->trashed()) {
            return response()->json([
                'message' => 'Ce bien a été supprimé'
            ], 404);
        }

        // Charger les relations nécessaires
        $bien->load(['localisation', 'user']);

        return response()->json([
            'bien' => [
                'id' => $bien->id,
                'code_inventaire' => $bien->code_inventaire,
                'designation' => $bien->designation,
                'nature' => $bien->nature,
                'date_acquisition' => $bien->date_acquisition?->format('Y-m-d'),
                'service_usager' => $bien->service_usager,
                'localisation_id' => $bien->localisation_id,
                'valeur_acquisition' => $bien->valeur_acquisition,
                'etat' => $bien->etat,
                'qr_code_path' => $bien->qr_code_path,
                'observation' => $bien->observation,
                'user_id' => $bien->user_id,
                'localisation' => $bien->localisation ? [
                    'id' => $bien->localisation->id,
                    'code' => $bien->localisation->code,
                    'designation' => $bien->localisation->designation,
                    'batiment' => $bien->localisation->batiment,
                    'etage' => $bien->localisation->etage,
                ] : null,
                'enregistre_par' => $bien->user ? $bien->user->name : null,
            ]
        ]);
    }

    /**
     * Récupérer un bien par son code inventaire
     * Utilisé lors du scan QR code d'un bien
     * 
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCode($code)
    {
        $bien = Bien::where('code_inventaire', $code)
            ->whereNull('deleted_at')
            ->with(['localisation', 'user'])
            ->first();

        if (!$bien) {
            return response()->json([
                'message' => 'Bien non trouvé'
            ], 404);
        }

        return response()->json([
            'bien' => [
                'id' => $bien->id,
                'code_inventaire' => $bien->code_inventaire,
                'designation' => $bien->designation,
                'nature' => $bien->nature,
                'date_acquisition' => $bien->date_acquisition?->format('Y-m-d'),
                'service_usager' => $bien->service_usager,
                'localisation_id' => $bien->localisation_id,
                'valeur_acquisition' => $bien->valeur_acquisition,
                'etat' => $bien->etat,
                'qr_code_path' => $bien->qr_code_path,
                'observation' => $bien->observation,
                'localisation' => $bien->localisation ? [
                    'id' => $bien->localisation->id,
                    'code' => $bien->localisation->code,
                    'designation' => $bien->localisation->designation,
                ] : null,
            ]
        ]);
    }
}
