<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CollecteBienInitiale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollecteInitialeController extends Controller
{
    /**
     * Enregistre un lot de collecte initiale dans une table autonome.
     */
    public function enregistrerLot(Request $request): JsonResponse
    {
        $currentYear = (int) date('Y');

        $validated = $request->validate([
            'lot_uid' => 'required|uuid',
            'emplacement_label' => 'required|string|max:255',
            'affectation_label' => 'nullable|string|max:255',
            'localisation_label' => 'nullable|string|max:255',
            'agent_label' => 'nullable|string|max:255',
            'items' => 'required|array|min:1|max:500',
            'items.*.designation' => 'required|string|max:255',
            'items.*.quantite' => 'nullable|integer|min:1|max:10000',
            'items.*.etat' => 'nullable|in:neuf,bon,moyen,mauvais',
            'items.*.date_acquisition' => 'nullable|integer|min:1900|max:' . ($currentYear + 1),
            'items.*.observations' => 'nullable|string|max:2000',
            'items.*.transcription_brute' => 'nullable|string|max:4000',
            'items.*.confiance' => 'nullable|numeric|min:0|max:100',
        ]);

        $lotUid = $validated['lot_uid'];
        $affectationLabel = trim((string) ($validated['affectation_label'] ?? 'Non renseignee'));

        // Idempotence: si le lot existe deja, on retourne un recap sans reinsertion.
        $existing = CollecteBienInitiale::where('lot_uid', $lotUid)
            ->orderBy('line_index')
            ->get();

        if ($existing->isNotEmpty()) {
            return response()->json([
                'message' => 'Lot deja enregistre',
                'lot_uid' => $lotUid,
                'resume' => [
                    'items_recus' => count($validated['items']),
                    'lignes_enregistrees' => $existing->count(),
                    'items_rejetes' => 0,
                ],
                'ids_collecte_crees' => $existing->pluck('id')->all(),
            ], 200);
        }

        $createdIds = [];

        DB::transaction(function () use ($validated, $lotUid, $affectationLabel, $request, &$createdIds): void {
            $rows = [];
            $now = now();
            $createdBy = $request->user()->idUser ?? null;

            foreach ($validated['items'] as $index => $item) {
                $rows[] = [
                    'lot_uid' => $lotUid,
                    'line_index' => $index + 1,
                    'emplacement_label' => trim($validated['emplacement_label']),
                    'affectation_label' => $affectationLabel,
                    'localisation_label' => isset($validated['localisation_label']) ? trim((string) $validated['localisation_label']) : null,
                    'designation' => trim($item['designation']),
                    'quantite' => (int) ($item['quantite'] ?? 1),
                    'etat' => $item['etat'] ?? null,
                    'date_acquisition' => $item['date_acquisition'] ?? null,
                    'observations' => $item['observations'] ?? null,
                    'transcription_brute' => $item['transcription_brute'] ?? null,
                    'confiance' => $item['confiance'] ?? null,
                    'agent_label' => $validated['agent_label'] ?? null,
                    'created_by_user_id' => $createdBy,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            CollecteBienInitiale::insert($rows);

            $createdIds = CollecteBienInitiale::where('lot_uid', $lotUid)
                ->orderBy('line_index')
                ->pluck('id')
                ->all();
        });

        return response()->json([
            'message' => 'Lot enregistre avec succes',
            'lot_uid' => $lotUid,
            'emplacement_label' => $validated['emplacement_label'],
            'affectation_label' => $affectationLabel,
            'resume' => [
                'items_recus' => count($validated['items']),
                'lignes_enregistrees' => count($createdIds),
                'items_rejetes' => 0,
            ],
            'ids_collecte_crees' => $createdIds,
        ], 201);
    }
}
