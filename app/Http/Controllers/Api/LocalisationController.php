<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Localisation;
use App\Models\Bien;
use Illuminate\Http\Request;

/**
 * Controller API pour la gestion des localisations depuis la PWA
 * Gère les opérations de localisation via l'API
 */
class LocalisationController extends Controller
{
    /**
     * Récupérer une localisation par son code
     * Utilisé lors du scan QR code d'une localisation
     * 
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCode($code)
    {
        $localisation = Localisation::where('code', $code)
            ->where('actif', true)
            ->first();

        if (!$localisation) {
            return response()->json([
                'message' => 'Localisation non trouvée'
            ], 404);
        }

        return response()->json([
            'localisation' => [
                'id' => $localisation->id,
                'code' => $localisation->code,
                'designation' => $localisation->designation,
                'batiment' => $localisation->batiment,
                'etage' => $localisation->etage,
                'service_rattache' => $localisation->service_rattache,
                'responsable' => $localisation->responsable,
                'qr_code_path' => $localisation->qr_code_path,
            ]
        ]);
    }

    /**
     * Récupérer tous les biens d'une localisation
     * Utilisé pour charger les biens attendus lors du scan d'une localisation
     * 
     * @param Localisation $localisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function biens(Localisation $localisation)
    {
        // Vérifier que la localisation est active
        if (!$localisation->actif) {
            return response()->json([
                'message' => 'Localisation inactive'
            ], 403);
        }

        $biens = Bien::where('localisation_id', $localisation->id)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($bien) {
                return [
                    'id' => $bien->id,
                    'code_inventaire' => $bien->code_inventaire,
                    'designation' => $bien->designation,
                    'nature' => $bien->nature,
                    'service_usager' => $bien->service_usager,
                    'localisation_id' => $bien->localisation_id,
                    'valeur_acquisition' => $bien->valeur_acquisition,
                    'etat' => $bien->etat,
                    'date_acquisition' => $bien->date_acquisition?->format('Y-m-d'),
                    'qr_code_path' => $bien->qr_code_path,
                ];
            });

        return response()->json([
            'biens' => $biens,
            'total' => $biens->count()
        ]);
    }

    /**
     * Récupérer les détails d'une localisation
     * 
     * @param Localisation $localisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Localisation $localisation)
    {
        $localisation->loadCount('biens');

        return response()->json([
            'localisation' => [
                'id' => $localisation->id,
                'code' => $localisation->code,
                'designation' => $localisation->designation,
                'batiment' => $localisation->batiment,
                'etage' => $localisation->etage,
                'service_rattache' => $localisation->service_rattache,
                'responsable' => $localisation->responsable,
                'qr_code_path' => $localisation->qr_code_path,
                'actif' => $localisation->actif,
                'biens_count' => $localisation->biens_count,
            ]
        ]);
    }
}
